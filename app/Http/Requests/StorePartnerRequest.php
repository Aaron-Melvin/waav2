<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePartnerRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', 'alpha_dash', 'unique:partners,slug'],
            'billing_email' => ['required', 'email', 'max:255'],
            'currency' => ['required', 'string', 'size:3'],
            'timezone' => ['required', 'string', 'max:64'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive', 'pending'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'A partner name is required.',
            'name.max' => 'Partner names may not exceed 150 characters.',
            'slug.required' => 'A partner slug is required.',
            'slug.alpha_dash' => 'Partner slugs may only contain letters, numbers, dashes, and underscores.',
            'slug.unique' => 'That partner slug is already in use.',
            'billing_email.required' => 'A billing email address is required.',
            'billing_email.email' => 'Please provide a valid billing email address.',
            'billing_email.max' => 'Billing emails may not exceed 255 characters.',
            'currency.required' => 'A currency code is required.',
            'currency.size' => 'Currency must be a 3-letter ISO code.',
            'timezone.required' => 'A timezone is required.',
            'timezone.max' => 'Timezone values may not exceed 64 characters.',
            'status.required' => 'A partner status is required.',
            'status.in' => 'Status must be active, inactive, or pending.',
        ];
    }
}
