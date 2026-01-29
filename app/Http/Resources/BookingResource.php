<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'status' => $this->status,
            'channel' => $this->channel,
            'currency' => $this->currency,
            'booking_reference' => $this->booking_reference,
            'payment_status' => $this->payment_status,
            'totals' => [
                'gross' => $this->total_gross,
                'tax' => $this->total_tax,
                'fees' => $this->total_fees,
            ],
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'items' => BookingItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
