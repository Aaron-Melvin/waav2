<?php

namespace App\Http\Requests\Front;

use App\Models\Partner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHoldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Partner|null $partner */
        $partner = $this->attributes->get('currentPartner');
        $partnerId = $partner?->id;

        return [
            'product_id' => [
                'required',
                'uuid',
                Rule::exists('products', 'id')->where('partner_id', $partnerId),
            ],
            'event_id' => [
                'nullable',
                'uuid',
                Rule::exists('events', 'id')->where('partner_id', $partnerId),
            ],
            'unit_id' => [
                'nullable',
                'uuid',
                Rule::exists('units', 'id')->where('partner_id', $partnerId),
            ],
            'date' => ['nullable', 'date'],
            'starts_on' => ['nullable', 'date', 'required_without:event_id'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'expires_in_minutes' => ['nullable', 'integer', 'min:5', 'max:120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'A product is required to create a hold.',
            'product_id.exists' => 'The selected product is invalid.',
            'event_id.exists' => 'The selected event is invalid.',
            'unit_id.exists' => 'The selected unit is invalid.',
            'starts_on.required_without' => 'A start date is required when no event is supplied.',
            'ends_on.after_or_equal' => 'The end date must be on or after the start date.',
            'quantity.min' => 'Quantity must be at least 1.',
            'expires_in_minutes.min' => 'Hold expiration must be at least 5 minutes.',
            'expires_in_minutes.max' => 'Hold expiration may not exceed 120 minutes.',
        ];
    }
}
