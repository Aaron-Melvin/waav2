<?php

namespace App\Http\Requests\Partner;

use App\Models\Partner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIcalFeedRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'product_id' => [
                'nullable',
                'uuid',
                'required_without:unit_id',
                Rule::exists('products', 'id')->where('partner_id', $partnerId),
            ],
            'unit_id' => [
                'nullable',
                'uuid',
                'required_without:product_id',
                Rule::exists('units', 'id')->where('partner_id', $partnerId),
            ],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
            'meta' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'A feed name is required.',
            'product_id.required_without' => 'A product or unit is required for the feed.',
            'unit_id.required_without' => 'A product or unit is required for the feed.',
            'product_id.exists' => 'The selected product is invalid.',
            'unit_id.exists' => 'The selected unit is invalid.',
            'status.in' => 'Status must be active or inactive.',
        ];
    }
}
