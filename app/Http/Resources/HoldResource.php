<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HoldResource extends JsonResource
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
            'product_id' => $this->product_id,
            'event_id' => $this->event_id,
            'unit_id' => $this->unit_id,
            'starts_on' => $this->starts_on?->toDateString(),
            'ends_on' => $this->ends_on?->toDateString(),
            'quantity' => $this->quantity,
            'status' => $this->status,
            'expires_at' => $this->expires_at?->toIso8601String(),
            'meta' => $this->meta,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
