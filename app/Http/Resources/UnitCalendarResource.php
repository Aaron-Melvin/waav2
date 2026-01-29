<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitCalendarResource extends JsonResource
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
            'unit_id' => $this->unit_id,
            'date' => $this->date?->toDateString(),
            'is_available' => $this->is_available,
            'min_stay_nights' => $this->min_stay_nights,
            'max_stay_nights' => $this->max_stay_nights,
            'reason' => $this->reason,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
