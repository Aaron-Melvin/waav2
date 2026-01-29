<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRefundRequest;
use App\Http\Resources\RefundResource;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Http\JsonResponse;

class PaymentRefundController extends Controller
{
    public function store(StoreRefundRequest $request, Payment $payment): JsonResponse
    {
        $payload = $request->validated();

        $refund = Refund::query()->create([
            'payment_id' => $payment->id,
            'amount' => $payload['amount'],
            'currency' => $payment->currency,
            'status' => $payload['status'] ?? 'pending',
            'provider_refund_id' => $payload['provider_refund_id'] ?? null,
            'reason' => $payload['reason'] ?? null,
            'raw_payload' => $payload['raw_payload'] ?? null,
        ]);

        return (new RefundResource($refund))
            ->response()
            ->setStatusCode(201);
    }
}
