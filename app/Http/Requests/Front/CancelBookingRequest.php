<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class CancelBookingRequest extends FormRequest
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
            'reason' => ['nullable', 'string', 'max:255'],
            'refund' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reason.max' => 'Cancellation reasons may not exceed 255 characters.',
        ];
    }
}
