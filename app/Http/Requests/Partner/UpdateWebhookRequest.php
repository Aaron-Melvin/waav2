<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWebhookRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:150'],
            'url' => ['sometimes', 'url', 'max:2048'],
            'events' => ['sometimes', 'array', 'min:1'],
            'events.*' => ['string', 'max:150'],
            'secret' => ['sometimes', 'nullable', 'string', 'max:255'],
            'headers' => ['sometimes', 'array'],
            'status' => ['sometimes', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.max' => 'Webhook names may not exceed 150 characters.',
            'url.url' => 'Webhook URLs must be valid URLs.',
            'events.min' => 'At least one event is required.',
            'events.*.max' => 'Event names may not exceed 150 characters.',
            'status.in' => 'Status must be active or inactive.',
        ];
    }
}
