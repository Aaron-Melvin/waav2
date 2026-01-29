<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\CancelBookingRequest;
use App\Http\Requests\Front\ConfirmBookingRequest;
use App\Http\Requests\Front\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\BookingAllocation;
use App\Models\BookingItem;
use App\Models\BookingStatusHistory;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Partner;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Unit;
use App\Models\UnitBookingLock;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $customer = Customer::query()->firstOrCreate([
            'partner_id' => $partner->id,
            'email' => $payload['customer']['email'],
        ], [
            'name' => $payload['customer']['name'],
            'phone_e164' => $payload['customer']['phone_e164'] ?? null,
        ]);

        $customer->fill([
            'name' => $payload['customer']['name'],
            'phone_e164' => $payload['customer']['phone_e164'] ?? $customer->phone_e164,
        ])->save();

        $coupon = $this->resolveCoupon($partner, $payload['coupon_code'] ?? null);

        if (($payload['coupon_code'] ?? null) && ! $coupon) {
            return response()->json([
                'message' => 'Coupon code is invalid or expired.',
                'errors' => [
                    'coupon_code' => ['Coupon code is invalid or expired.'],
                ],
            ], 422);
        }

        $booking = Booking::query()->create([
            'partner_id' => $partner->id,
            'customer_id' => $customer->id,
            'coupon_id' => $coupon?->id,
            'status' => 'draft',
            'channel' => 'front',
            'currency' => $partner->currency ?? 'EUR',
            'total_gross' => 0,
            'total_tax' => 0,
            'total_fees' => 0,
            'payment_status' => 'unpaid',
            'booking_reference' => $this->generateBookingReference(),
            'terms_version' => $payload['terms_version'],
            'meta' => [
                'source' => 'front',
            ],
        ]);

        $totals = $this->createBookingItems($booking, $payload['items'], $partner);

        $booking->update([
            'total_gross' => $totals['gross'],
            'total_tax' => $totals['tax'],
            'total_fees' => $totals['fees'],
        ]);

        BookingStatusHistory::query()->create([
            'booking_id' => $booking->id,
            'from_status' => null,
            'to_status' => 'draft',
            'reason' => 'Created from front checkout',
        ]);

        $booking->load(['customer', 'items']);

        $response = (new BookingResource($booking))
            ->response()
            ->setStatusCode(201);

        return $response;
    }

    public function confirm(ConfirmBookingRequest $request, string $booking): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $bookingModel = Booking::query()
            ->where('partner_id', $partner->id)
            ->with(['items', 'customer'])
            ->findOrFail($booking);

        if (! in_array($bookingModel->status, ['draft', 'pending_payment'], true)) {
            return response()->json([
                'message' => 'Booking cannot be confirmed from its current status.',
            ], 409);
        }

        $fromStatus = $bookingModel->status;
        $paymentStatus = ($payload['status'] ?? 'captured') === 'captured' ? 'paid' : 'pending';

        $bookingModel->update([
            'status' => 'confirmed',
            'payment_status' => $paymentStatus,
        ]);

        $payment = Payment::query()->create([
            'partner_id' => $partner->id,
            'booking_id' => $bookingModel->id,
            'provider' => $payload['payment_method'],
            'provider_payment_id' => $payload['payment_token'] ?? null,
            'amount' => $bookingModel->total_gross,
            'currency' => $bookingModel->currency,
            'status' => $payload['status'] ?? 'captured',
            'captured_at' => ($payload['status'] ?? 'captured') === 'captured'
                ? CarbonImmutable::now()
                : null,
        ]);

        Invoice::query()->create([
            'partner_id' => $partner->id,
            'booking_id' => $bookingModel->id,
            'number' => $this->generateInvoiceNumber(),
            'currency' => $bookingModel->currency,
            'total_gross' => $bookingModel->total_gross,
            'total_tax' => $bookingModel->total_tax,
            'total_fees' => $bookingModel->total_fees,
            'status' => 'issued',
            'issued_at' => CarbonImmutable::now(),
            'due_at' => CarbonImmutable::now()->addDays(14),
            'meta' => [
                'payment_id' => $payment->id,
            ],
        ]);

        $this->ensureBookingAllocations($bookingModel);
        $this->createUnitLocks($bookingModel);

        BookingStatusHistory::query()->create([
            'booking_id' => $bookingModel->id,
            'from_status' => $fromStatus,
            'to_status' => 'confirmed',
            'reason' => 'Payment confirmed',
        ]);

        $bookingModel->load(['customer', 'items']);

        $response = (new BookingResource($bookingModel))
            ->response();

        return $response;
    }

    public function cancel(CancelBookingRequest $request, string $booking): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();
        $refundRequested = (bool) ($payload['refund'] ?? false);

        $bookingModel = Booking::query()
            ->where('partner_id', $partner->id)
            ->with(['items', 'customer'])
            ->findOrFail($booking);

        $fromStatus = $bookingModel->status;

        $bookingModel->update([
            'status' => 'cancelled',
            'payment_status' => $refundRequested ? 'refunded' : $bookingModel->payment_status,
        ]);

        UnitBookingLock::query()
            ->where('booking_id', $bookingModel->id)
            ->delete();

        $refund = null;
        if ($refundRequested) {
            $payment = Payment::query()
                ->where('booking_id', $bookingModel->id)
                ->latest()
                ->first();

            if ($payment) {
                $refund = Refund::query()->create([
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'status' => 'pending',
                    'reason' => $payload['reason'] ?? null,
                ]);

                $payment->update([
                    'status' => 'refunded',
                ]);
            }
        }

        BookingStatusHistory::query()->create([
            'booking_id' => $bookingModel->id,
            'from_status' => $fromStatus,
            'to_status' => 'cancelled',
            'reason' => $payload['reason'] ?? 'Cancelled',
            'meta' => $refund ? ['refund_id' => $refund->id] : null,
        ]);

        $bookingModel->load(['customer', 'items']);

        return (new BookingResource($bookingModel))
            ->response();
    }

    protected function resolveCoupon(Partner $partner, ?string $code): ?Coupon
    {
        if (! $code) {
            return null;
        }

        return Coupon::query()
            ->where('partner_id', $partner->id)
            ->where('code', $code)
            ->where('status', 'active')
            ->where(function ($query) {
                $query
                    ->whereNull('starts_on')
                    ->orWhereDate('starts_on', '<=', CarbonImmutable::today());
            })
            ->where(function ($query) {
                $query
                    ->whereNull('ends_on')
                    ->orWhereDate('ends_on', '>=', CarbonImmutable::today());
            })
            ->first();
    }

    protected function generateBookingReference(): string
    {
        return 'WAA-'.Str::upper(Str::random(6));
    }

    protected function generateInvoiceNumber(): string
    {
        do {
            $number = 'INV-'.Str::upper(Str::random(6));
        } while (Invoice::query()->where('number', $number)->exists());

        return $number;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array{gross: float, tax: float, fees: float}
     */
    protected function createBookingItems(Booking $booking, array $items, Partner $partner): array
    {
        $grossTotal = 0.0;
        $taxTotal = 0.0;
        $feeTotal = 0.0;

        foreach ($items as $item) {
            $product = Product::query()
                ->where('partner_id', $partner->id)
                ->findOrFail($item['product_id']);

            $event = null;
            if (! empty($item['event_id'])) {
                $event = Event::query()
                    ->where('partner_id', $partner->id)
                    ->findOrFail($item['event_id']);

                if ($event->product_id !== $product->id) {
                    abort(422, 'Event does not belong to the selected product.');
                }
            }

            $unit = null;
            if (! empty($item['unit_id'])) {
                $unit = Unit::query()
                    ->where('partner_id', $partner->id)
                    ->findOrFail($item['unit_id']);

                if ($unit->product_id !== $product->id) {
                    abort(422, 'Unit does not belong to the selected product.');
                }
            }

            $startsOn = $item['starts_on'] ?? $event?->starts_at?->toDateString();
            $endsOn = $item['ends_on'] ?? $event?->ends_at?->toDateString() ?? $startsOn;
            $quantity = (int) $item['quantity'];
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $total = (float) ($item['total'] ?? ($unitPrice * $quantity));

            $grossTotal += $total;

            $bookingItem = BookingItem::query()->create([
                'booking_id' => $booking->id,
                'product_id' => $product->id,
                'event_id' => $event?->id,
                'unit_id' => $unit?->id,
                'item_type' => $item['item_type'],
                'starts_on' => $startsOn,
                'ends_on' => $endsOn,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => $total,
                'meta' => $item['meta'] ?? null,
            ]);

            BookingAllocation::query()->firstOrCreate([
                'booking_id' => $booking->id,
                'event_id' => $event?->id,
                'unit_id' => $unit?->id,
            ], [
                'quantity' => $bookingItem->quantity,
            ]);
        }

        return [
            'gross' => $grossTotal,
            'tax' => $taxTotal,
            'fees' => $feeTotal,
        ];
    }

    protected function ensureBookingAllocations(Booking $booking): void
    {
        $booking->loadMissing('items');

        foreach ($booking->items as $item) {
            BookingAllocation::query()->firstOrCreate([
                'booking_id' => $booking->id,
                'event_id' => $item->event_id,
                'unit_id' => $item->unit_id,
            ], [
                'quantity' => $item->quantity,
            ]);
        }
    }

    protected function createUnitLocks(Booking $booking): void
    {
        $booking->loadMissing('items');

        foreach ($booking->items as $item) {
            if (! $item->unit_id || ! $item->starts_on || ! $item->ends_on) {
                continue;
            }

            $start = CarbonImmutable::parse($item->starts_on);
            $end = CarbonImmutable::parse($item->ends_on);

            for ($date = $start; $date->lessThanOrEqualTo($end); $date = $date->addDay()) {
                UnitBookingLock::query()->firstOrCreate([
                    'booking_id' => $booking->id,
                    'unit_id' => $item->unit_id,
                    'date' => $date->toDateString(),
                ]);
            }
        }
    }
}
