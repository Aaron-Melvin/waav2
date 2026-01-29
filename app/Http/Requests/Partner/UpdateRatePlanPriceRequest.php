<?php

namespace App\Http\Requests\Partner;

use App\Models\RatePlan;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRatePlanPriceRequest extends FormRequest
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
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after_or_equal:starts_on'],
            'price' => ['required', 'numeric', 'min:0'],
            'extra_adult' => ['nullable', 'numeric', 'min:0'],
            'extra_child' => ['nullable', 'numeric', 'min:0'],
            'restrictions' => ['nullable', 'json'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messagesFor(): array
    {
        return [
            'starts_on.required' => 'A start date is required.',
            'starts_on.date' => 'Start date must be a valid date.',
            'ends_on.required' => 'An end date is required.',
            'ends_on.date' => 'End date must be a valid date.',
            'ends_on.after_or_equal' => 'End date must be the same as or after the start date.',
            'price.required' => 'A nightly price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price cannot be negative.',
            'extra_adult.numeric' => 'Extra adult pricing must be a number.',
            'extra_adult.min' => 'Extra adult pricing cannot be negative.',
            'extra_child.numeric' => 'Extra child pricing must be a number.',
            'extra_child.min' => 'Extra child pricing cannot be negative.',
            'restrictions.json' => 'Restrictions must be valid JSON.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $ratePlan = $this->route('ratePlan');

        if (is_string($ratePlan)) {
            $partner = $this->attributes->get('currentPartner');
            $ratePlan = RatePlan::query()
                ->when($partner?->id, fn ($query) => $query->where('partner_id', $partner->id))
                ->find($ratePlan);
        }

        if ($ratePlan instanceof RatePlan) {
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
