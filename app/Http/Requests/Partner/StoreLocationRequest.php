<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLocationRequest extends FormRequest
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
            'address_line_1' => ['nullable', 'string', 'max:150'],
            'address_line_2' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:150'],
            'region' => ['nullable', 'string', 'max:150'],
            'postal_code' => ['nullable', 'string', 'max:32'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'timezone' => ['required', 'string', 'max:64'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messagesFor(): array
    {
        return [
            'name.required' => 'A location name is required.',
            'name.max' => 'Location names may not exceed 150 characters.',
            'address_line_1.max' => 'Address lines may not exceed 150 characters.',
            'address_line_2.max' => 'Address lines may not exceed 150 characters.',
            'city.max' => 'City names may not exceed 150 characters.',
            'region.max' => 'Region names may not exceed 150 characters.',
            'postal_code.max' => 'Postal codes may not exceed 32 characters.',
            'country_code.size' => 'Country codes must be 2 characters.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'timezone.required' => 'A timezone is required.',
            'timezone.max' => 'Timezone values may not exceed 64 characters.',
            'status.in' => 'Status must be active or inactive.',
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
