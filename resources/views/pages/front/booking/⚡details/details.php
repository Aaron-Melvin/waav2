<?php

use App\Models\Booking;
use App\Models\BookingAllocation;
use App\Models\BookingItem;
use App\Models\BookingStatusHistory;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Hold;
use App\Models\SearchIndex;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::front'), Title('Complete your booking')] class extends Component
{
    public Hold $hold;

    public ?string $customerName = null;

    public ?string $customerEmail = null;

    public ?string $customerPhone = null;

    public ?string $couponCode = null;

    public string $termsVersion = '2026-01';

    public bool $acceptTerms = false;

    public ?float $unitPrice = null;

    public ?string $errorMessage = null;

    public function mount(Hold $hold): void
    {
        $this->hold = $hold->loadMissing(['product.location', 'partner', 'event', 'unit']);
        $this->unitPrice = $this->resolveUnitPrice();
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'customerName' => ['required', 'string', 'max:150'],
            'customerEmail' => ['required', 'email', 'max:255'],
            'customerPhone' => ['nullable', 'string', 'max:32'],
            'couponCode' => ['nullable', 'string', 'max:50'],
            'acceptTerms' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'customerName.required' => 'Customer name is required.',
            'customerEmail.required' => 'Customer email is required.',
            'acceptTerms.accepted' => 'You must accept the current terms to proceed.',
        ];
    }

    public function createBooking(): void
    {
        $this->errorMessage = null;

        if ($this->isExpired) {
            $this->errorMessage = 'This hold has expired. Please start a new search.';
            return;
        }

        $this->validate();

        $partner = $this->hold->partner;

        if (! $partner) {
            $this->errorMessage = 'Partner context is missing.';
            return;
        }

        $customer = Customer::query()->firstOrCreate([
            'partner_id' => $partner->id,
            'email' => $this->customerEmail,
        ], [
            'name' => $this->customerName,
            'phone_e164' => $this->customerPhone,
        ]);

        $customer->fill([
            'name' => $this->customerName,
            'phone_e164' => $this->customerPhone ?: $customer->phone_e164,
        ])->save();

        $coupon = $this->resolveCoupon($partner, $this->couponCode);

        if ($this->couponCode && ! $coupon) {
            $this->errorMessage = 'Coupon code is invalid or expired.';
            return;
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
            'terms_version' => $this->termsVersion,
            'meta' => [
                'source' => 'front-ui',
            ],
        ]);

        $totals = $this->createBookingItem($booking);

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

        $this->hold->update([
            'status' => 'converted',
            'meta' => array_merge($this->hold->meta ?? [], [
                'booking_id' => $booking->id,
            ]),
        ]);

        $this->redirectRoute('front.booking.confirm', ['booking' => $booking->id]);
    }

    /**
     * @return array{gross: float, tax: float, fees: float}
     */
    protected function createBookingItem(Booking $booking): array
    {
        $product = $this->hold->product;

        $unitPrice = (float) ($this->unitPrice ?? 0);
        $quantity = (int) ($this->hold->quantity ?? 1);
        $total = $unitPrice * $quantity;

        BookingItem::query()->create([
            'booking_id' => $booking->id,
            'product_id' => $this->hold->product_id,
            'event_id' => $this->hold->event_id,
            'unit_id' => $this->hold->unit_id,
            'item_type' => $product?->type ?? 'event',
            'starts_on' => $this->hold->starts_on,
            'ends_on' => $this->hold->ends_on ?? $this->hold->starts_on,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $total,
            'meta' => [
                'hold_id' => $this->hold->id,
            ],
        ]);

        BookingAllocation::query()->firstOrCreate([
            'booking_id' => $booking->id,
            'event_id' => $this->hold->event_id,
            'unit_id' => $this->hold->unit_id,
        ], [
            'quantity' => $quantity,
        ]);

        return [
            'gross' => $total,
            'tax' => 0.0,
            'fees' => 0.0,
        ];
    }

    protected function resolveCoupon(\App\Models\Partner $partner, ?string $code): ?Coupon
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

    protected function resolveUnitPrice(): ?float
    {
        $price = SearchIndex::query()
            ->where('product_id', $this->hold->product_id)
            ->when($this->hold->event_id, fn ($query) => $query->where('event_id', $this->hold->event_id))
            ->when($this->hold->unit_id, fn ($query) => $query->where('unit_id', $this->hold->unit_id))
            ->whereDate('starts_on', '<=', $this->hold->starts_on)
            ->whereDate('ends_on', '>=', $this->hold->ends_on ?? $this->hold->starts_on)
            ->orderBy('price_min')
            ->value('price_min');

        return $price !== null ? (float) $price : null;
    }

    public function getIsExpiredProperty(): bool
    {
        return $this->hold->expires_at?->isPast() ?? false;
    }

    public function getEstimateTotalProperty(): float
    {
        $unitPrice = (float) ($this->unitPrice ?? 0);
        $quantity = (int) ($this->hold->quantity ?? 1);

        return $unitPrice * $quantity;
    }
};
