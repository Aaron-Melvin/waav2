<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCalendarSyncAccountRequest extends FormRequest
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
            'provider' => ['required', 'string', 'max:50', Rule::in(['google', 'outlook', 'ical'])],
            'external_id' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
            'access_token' => ['nullable', 'string'],
            'refresh_token' => ['nullable', 'string'],
            'token_expires_at' => ['nullable', 'date'],
            'meta' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'provider.required' => 'A calendar provider is required.',
            'provider.in' => 'Provider must be google, outlook, or ical.',
            'email.email' => 'Please provide a valid email address.',
            'status.in' => 'Status must be active or inactive.',
        ];
    }
}
