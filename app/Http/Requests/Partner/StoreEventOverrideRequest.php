<?php

namespace App\Http\Requests\Partner;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventOverrideRequest extends FormRequest
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
    public static function rulesFor(string $field): array
    {
        $valueRules = ['required'];

        if ($field === 'capacity_total') {
            $valueRules[] = 'integer';
            $valueRules[] = 'min:0';
        } elseif ($field === 'price_override') {
            $valueRules[] = 'numeric';
            $valueRules[] = 'min:0';
        } else {
            $valueRules[] = 'string';
            $valueRules[] = 'max:255';
        }

        return [
            'field' => ['required', 'string', Rule::in(['capacity_total', 'price_override', 'notes'])],
            'value' => $valueRules,
            'currency' => ['nullable', Rule::requiredIf($field === 'price_override'), 'string', 'size:3'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messagesFor(): array
    {
        return [
            'field.required' => 'An override field is required.',
            'field.in' => 'Overrides must be for capacity, price, or notes.',
            'value.required' => 'An override value is required.',
            'value.integer' => 'Capacity overrides must be a whole number.',
            'value.numeric' => 'Price overrides must be a number.',
            'value.min' => 'Override values cannot be negative.',
            'value.string' => 'Notes must be text.',
            'value.max' => 'Notes may not exceed 255 characters.',
            'currency.required' => 'A currency is required for price overrides.',
            'currency.size' => 'Currency codes must be 3 characters.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $event = $this->route('event');

        if (is_string($event)) {
            $partner = $this->attributes->get('currentPartner');
            $event = Event::query()
                ->when($partner?->id, fn ($query) => $query->where('partner_id', $partner->id))
                ->find($event);
        }

        if ($event instanceof Event) {
            $field = (string) $this->input('field');

            return self::rulesFor($field);
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
