<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'event_series_id' => $this->event_series_id,
            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'capacity_total' => $this->capacity_total,
            'capacity_reserved' => $this->capacity_reserved,
            'traffic_light' => $this->traffic_light,
            'status' => $this->status,
            'publish_state' => $this->publish_state,
            'weather_alert' => $this->weather_alert,
            'overrides' => EventOverrideResource::collection($this->whenLoaded('overrides')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
