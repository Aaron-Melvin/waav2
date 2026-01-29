<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'location_id' => $this->location_id,
            'name' => $this->name,
            'type' => $this->type,
            'slug' => $this->slug,
            'description' => $this->description,
            'capacity_total' => $this->capacity_total,
            'default_currency' => $this->default_currency,
            'status' => $this->status,
            'visibility' => $this->visibility,
            'lead_time_minutes' => $this->lead_time_minutes,
            'cutoff_minutes' => $this->cutoff_minutes,
            'meta' => $this->meta,
            'location' => new LocationResource($this->whenLoaded('location')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
