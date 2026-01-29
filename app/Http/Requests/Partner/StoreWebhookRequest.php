<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWebhookRequest extends FormRequest
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
            'url' => ['required', 'url', 'max:2048'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['string', 'max:150'],
            'secret' => ['nullable', 'string', 'max:255'],
            'headers' => ['nullable', 'array'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'A webhook name is required.',
            'name.max' => 'Webhook names may not exceed 150 characters.',
            'url.required' => 'A webhook URL is required.',
            'url.url' => 'Webhook URLs must be valid URLs.',
            'events.required' => 'At least one event is required.',
            'events.min' => 'At least one event is required.',
            'events.*.max' => 'Event names may not exceed 150 characters.',
            'status.in' => 'Status must be active or inactive.',
        ];
    }
}
