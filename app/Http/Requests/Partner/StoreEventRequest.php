<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public static function rulesFor(?\App\Models\Partner $partner): array
    {
        $partnerId = $partner?->id;

        return [
            'product_id' => [
                'required',
                'uuid',
                Rule::exists('products', 'id')->where(function ($query) use ($partnerId): void {
                    if ($partnerId) {
                        $query->where('partner_id', $partnerId);
                    }
                }),
            ],
            'event_series_id' => [
                'nullable',
                'uuid',
                Rule::exists('event_series', 'id')->where(function ($query) use ($partnerId): void {
                    if ($partnerId) {
                        $query->where('partner_id', $partnerId);
                    }
                }),
            ],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'capacity_total' => ['nullable', 'integer', 'min:0'],
            'capacity_reserved' => ['nullable', 'integer', 'min:0'],
            'traffic_light' => ['nullable', 'string', Rule::in(['green', 'yellow', 'red'])],
            'status' => ['nullable', 'string', Rule::in(['scheduled', 'cancelled', 'completed'])],
            'publish_state' => ['nullable', 'string', Rule::in(['draft', 'published'])],
            'weather_alert' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messagesFor(): array
    {
        return [
            'product_id.required' => 'A product is required for this event.',
            'product_id.uuid' => 'Product IDs must be valid UUIDs.',
            'product_id.exists' => 'Product must belong to the current partner.',
            'event_series_id.uuid' => 'Event series IDs must be valid UUIDs.',
            'event_series_id.exists' => 'Event series must belong to the current partner.',
            'starts_at.required' => 'A start date/time is required.',
            'starts_at.date' => 'Start date/time must be valid.',
            'ends_at.required' => 'An end date/time is required.',
            'ends_at.date' => 'End date/time must be valid.',
            'ends_at.after' => 'End date/time must be after the start date/time.',
            'capacity_total.integer' => 'Total capacity must be a whole number.',
            'capacity_total.min' => 'Total capacity cannot be negative.',
            'capacity_reserved.integer' => 'Reserved capacity must be a whole number.',
            'capacity_reserved.min' => 'Reserved capacity cannot be negative.',
            'traffic_light.in' => 'Traffic light must be green, yellow, or red.',
            'status.in' => 'Status must be scheduled, cancelled, or completed.',
            'publish_state.in' => 'Publish state must be draft or published.',
            'weather_alert.boolean' => 'Weather alert must be true or false.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $partner = $this->attributes->get('currentPartner');

        return self::rulesFor($partner);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return self::messagesFor();
    }
}
