<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatePlanResource extends JsonResource
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
            'cancellation_policy_id' => $this->cancellation_policy_id,
            'name' => $this->name,
            'code' => $this->code,
            'pricing_model' => $this->pricing_model,
            'currency' => $this->currency,
            'status' => $this->status,
            'meta' => $this->meta,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
