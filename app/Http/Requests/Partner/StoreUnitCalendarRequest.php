<?php

namespace App\Http\Requests\Partner;

use App\Models\Unit;
use Illuminate\Foundation\Http\FormRequest;

class StoreUnitCalendarRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'is_available' => ['required', 'boolean'],
            'min_stay_nights' => ['nullable', 'integer', 'min:1'],
            'max_stay_nights' => ['nullable', 'integer', 'min:1', 'gte:min_stay_nights'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messagesFor(): array
    {
        return [
            'date.required' => 'A calendar date is required.',
            'date.date' => 'Calendar date must be a valid date.',
            'is_available.required' => 'Availability selection is required.',
            'is_available.boolean' => 'Availability must be true or false.',
            'min_stay_nights.integer' => 'Minimum stay must be a whole number.',
            'min_stay_nights.min' => 'Minimum stay must be at least 1 night.',
            'max_stay_nights.integer' => 'Maximum stay must be a whole number.',
            'max_stay_nights.min' => 'Maximum stay must be at least 1 night.',
            'max_stay_nights.gte' => 'Maximum stay must be greater than or equal to minimum stay.',
            'reason.max' => 'Reasons may not exceed 255 characters.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $unit = $this->route('unit');

        if (is_string($unit)) {
            $partner = $this->attributes->get('currentPartner');
            $unit = Unit::query()
                ->when($partner?->id, fn ($query) => $query->where('partner_id', $partner->id))
                ->find($unit);
        }

        if ($unit instanceof Unit) {
            return self::rulesFor();
        }

        return [];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return self::messagesFor();
    }
}
