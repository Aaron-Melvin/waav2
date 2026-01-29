<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingItemResource extends JsonResource
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
            'booking_id' => $this->booking_id,
            'product_id' => $this->product_id,
            'event_id' => $this->event_id,
            'unit_id' => $this->unit_id,
            'item_type' => $this->item_type,
            'starts_on' => $this->starts_on?->toDateString(),
            'ends_on' => $this->ends_on?->toDateString(),
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total' => $this->total,
            'meta' => $this->meta,
        ];
    }
}
