<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNotificationTemplateRequest extends FormRequest
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
        $channel = $this->input('channel');

        return [
            'name' => ['required', 'string', 'max:150'],
            'channel' => ['required', 'string', Rule::in(['email', 'sms'])],
            'locale' => ['nullable', 'string', 'max:8'],
            'subject' => [
                Rule::requiredIf($channel === 'email'),
                'nullable',
                'string',
                'max:255',
            ],
            'body' => ['required', 'string'],
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
            'name.required' => 'A template name is required.',
            'name.max' => 'Template names may not exceed 150 characters.',
            'channel.required' => 'A delivery channel is required.',
            'channel.in' => 'Channel must be email or sms.',
            'subject.required' => 'Email templates require a subject.',
            'subject.max' => 'Subjects may not exceed 255 characters.',
            'body.required' => 'Template content is required.',
            'locale.max' => 'Locale values may not exceed 8 characters.',
            'status.in' => 'Status must be active or inactive.',
        ];
    }
}
