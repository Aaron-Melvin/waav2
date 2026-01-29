<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCancellationPolicyRequest extends FormRequest
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
            'partner_id' => ['required', 'uuid', Rule::exists('partners', 'id')],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'rules' => ['required', 'array'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'partner_id.required' => 'A partner is required.',
            'partner_id.uuid' => 'Partner IDs must be valid UUIDs.',
            'partner_id.exists' => 'Partner must exist.',
            'name.required' => 'A policy name is required.',
            'name.max' => 'Policy names may not exceed 150 characters.',
            'rules.required' => 'Cancellation rules are required.',
            'rules.array' => 'Cancellation rules must be an array.',
            'status.in' => 'Status must be active or inactive.',
        ];
    }
}
