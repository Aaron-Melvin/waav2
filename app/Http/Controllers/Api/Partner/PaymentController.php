<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Partner;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends Controller
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
        $provider = trim($request->string('provider', '')->toString());
        $bookingId = trim($request->string('booking_id', '')->toString());

        $payments = Payment::query()
            ->where('partner_id', $partner->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($provider !== '', fn ($query) => $query->where('provider', $provider))
            ->when($bookingId !== '', fn ($query) => $query->where('booking_id', $bookingId))
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return PaymentResource::collection($payments);
    }

    public function show(Request $request, string $payment): PaymentResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $paymentModel = Payment::query()
            ->where('partner_id', $partner->id)
            ->with('refunds')
            ->findOrFail($payment);

        return new PaymentResource($paymentModel);
    }
}
