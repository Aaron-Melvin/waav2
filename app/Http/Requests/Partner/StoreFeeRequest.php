<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public static function rulesFor(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'type' => ['nullable', 'string', Rule::in(['flat', 'per_night', 'per_person'])],
            'amount' => ['required', 'numeric', 'min:0'],
            'applies_to' => ['required', 'string', 'max:100'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
            'meta' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messagesFor(): array
    {
        return [
            'name.required' => 'A fee name is required.',
            'name.max' => 'Fee names may not exceed 150 characters.',
            'type.in' => 'Fee type must be flat, per night, or per person.',
            'amount.required' => 'A fee amount is required.',
            'amount.numeric' => 'Fee amount must be a number.',
            'amount.min' => 'Fee amount cannot be negative.',
            'applies_to.required' => 'Applies-to is required.',
            'applies_to.max' => 'Applies-to values may not exceed 100 characters.',
            'status.in' => 'Status must be active or inactive.',
            'meta.array' => 'Meta must be an object.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return self::rulesFor();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return self::messagesFor();
    }
}
