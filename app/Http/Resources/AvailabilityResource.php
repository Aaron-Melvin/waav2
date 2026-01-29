<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailabilityResource extends JsonResource
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
            'product_id' => $this->product_id,
            'event_id' => $this->event_id,
            'unit_id' => $this->unit_id,
            'location_id' => $this->location_id,
            'starts_on' => $this->starts_on?->toDateString(),
            'ends_on' => $this->ends_on?->toDateString(),
            'capacity_total' => $this->capacity_total,
            'capacity_available' => $this->capacity_available,
            'price_min' => $this->price_min,
            'price_max' => $this->price_max,
            'currency' => $this->currency,
            'meta' => $this->meta,
        ];
    }
}
