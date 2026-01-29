<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventSeriesResource extends JsonResource
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
            'name' => $this->name,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'capacity_total' => $this->capacity_total,
            'timezone' => $this->timezone,
            'recurrence_rule' => $this->recurrence_rule,
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
