<?php

namespace App\Http\Requests\Partner;

use App\Models\EventBlackout;
use App\Models\Partner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventBlackoutRequest extends FormRequest
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
    public static function rulesFor(?Partner $partner): array
    {
        $partnerId = $partner?->id;

        return [
            'product_id' => [
                'required_without:location_id',
                'nullable',
                'uuid',
                Rule::exists('products', 'id')->where(function ($query) use ($partnerId): void {
                    if ($partnerId) {
                        $query->where('partner_id', $partnerId);
                    }
                }),
            ],
            'location_id' => [
                'required_without:product_id',
                'nullable',
                'uuid',
                Rule::exists('locations', 'id')->where(function ($query) use ($partnerId): void {
                    if ($partnerId) {
                        $query->where('partner_id', $partnerId);
                    }
                }),
            ],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'reason' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messagesFor(): array
    {
        return [
            'product_id.required_without' => 'Select a product or a location for this blackout.',
            'product_id.uuid' => 'Product IDs must be valid UUIDs.',
            'product_id.exists' => 'Product must belong to the current partner.',
            'location_id.required_without' => 'Select a product or a location for this blackout.',
            'location_id.uuid' => 'Location IDs must be valid UUIDs.',
            'location_id.exists' => 'Location must belong to the current partner.',
            'starts_at.required' => 'A blackout start date is required.',
            'starts_at.date' => 'Blackout start date must be valid.',
            'ends_at.required' => 'A blackout end date is required.',
            'ends_at.date' => 'Blackout end date must be valid.',
            'ends_at.after_or_equal' => 'Blackout end date must be on or after the start date.',
            'reason.max' => 'Reasons may not exceed 255 characters.',
            'status.required' => 'A blackout status is required.',
            'status.in' => 'Blackout status must be active or inactive.',
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
        $blackout = $this->route('blackout');

        if (is_string($blackout)) {
            $blackout = EventBlackout::query()
                ->when($partner?->id, fn ($query) => $query->where('partner_id', $partner->id))
                ->find($blackout);
        }

        if ($blackout instanceof EventBlackout) {
            return self::rulesFor($partner);
        }

        return [];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return self::messagesFor();
    }
}
