<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfirmBookingRequest extends FormRequest
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
            'payment_method' => ['required', 'string', 'max:50'],
            'payment_token' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(['captured', 'authorized'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'payment_method.required' => 'A payment method is required.',
            'payment_method.max' => 'Payment method may not exceed 50 characters.',
            'status.in' => 'Status must be captured or authorized.',
        ];
    }
}
