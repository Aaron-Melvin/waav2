<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\UpdateBookingStatusRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Partner;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookingController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $perPage = max(1, min($request->integer('per_page', 50), 100));
        $status = trim($request->string('status', '')->toString());
        $from = trim($request->string('from', '')->toString());
        $to = trim($request->string('to', '')->toString());

        $query = Booking::query()
            ->where('partner_id', $partner->id)
            ->with(['customer', 'items']);

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($from !== '') {
            $query->whereDate('created_at', '>=', CarbonImmutable::parse($from));
        }

        if ($to !== '') {
            $query->whereDate('created_at', '<=', CarbonImmutable::parse($to));
        }

        $bookings = $query
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return BookingResource::collection($bookings);
    }

    public function update(UpdateBookingStatusRequest $request, string $booking): BookingResource|JsonResponse
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
            ->with(['customer', 'items'])
            ->findOrFail($booking);

        $fromStatus = $bookingModel->status;

        $bookingModel->update([
            'status' => $payload['status'],
        ]);

        BookingStatusHistory::query()->create([
            'booking_id' => $bookingModel->id,
            'from_status' => $fromStatus,
            'to_status' => $payload['status'],
            'reason' => $payload['note'] ?? null,
        ]);

        return new BookingResource($bookingModel);
    }
}
