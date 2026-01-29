<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaxRequest extends FormRequest
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
            'rate' => ['required', 'numeric', 'min:0'],
            'applies_to' => ['required', 'string', 'max:100'],
            'is_inclusive' => ['nullable', 'boolean'],
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
            'name.required' => 'A tax name is required.',
            'name.max' => 'Tax names may not exceed 150 characters.',
            'rate.required' => 'A tax rate is required.',
            'rate.numeric' => 'Tax rate must be a number.',
            'rate.min' => 'Tax rate cannot be negative.',
            'applies_to.required' => 'Applies-to is required.',
            'applies_to.max' => 'Applies-to values may not exceed 100 characters.',
            'is_inclusive.boolean' => 'Inclusive flag must be true or false.',
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
