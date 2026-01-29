<?php

namespace App\Http\Requests\Front;

use App\Models\Partner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AvailabilitySearchRequest extends FormRequest
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
            'date_range' => ['required', 'array'],
            'date_range.from' => ['required', 'date'],
            'date_range.to' => ['required', 'date', 'after_or_equal:date_range.from'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'A product is required for availability searches.',
            'product_id.exists' => 'The selected product is invalid.',
            'date_range.required' => 'A date range is required.',
            'date_range.from.required' => 'A start date is required.',
            'date_range.to.required' => 'An end date is required.',
            'date_range.to.after_or_equal' => 'The end date must be on or after the start date.',
            'quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
