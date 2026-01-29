<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'partner_id' => $this->partner_id,
            'booking_id' => $this->booking_id,
            'provider' => $this->provider,
            'provider_payment_id' => $this->provider_payment_id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'captured_at' => $this->captured_at?->toIso8601String(),
            'raw_payload' => $this->raw_payload,
            'meta' => $this->meta,
            'refunds' => RefundResource::collection($this->whenLoaded('refunds')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
