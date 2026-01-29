<?php

namespace App\Http\Requests\Partner;

use App\Models\EventSeries;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventSeriesRequest extends FormRequest
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
    public static function rulesFor(string $partnerId): array
    {
        return [
            'product_id' => [
                'required',
                'uuid',
                Rule::exists('products', 'id')->where(function ($query) use ($partnerId): void {
                    $query->where('partner_id', $partnerId);
                }),
            ],
            'name' => ['required', 'string', 'max:150'],
            'starts_at' => ['required', 'date_format:H:i'],
            'ends_at' => ['required', 'date_format:H:i', 'after:starts_at'],
            'capacity_total' => ['nullable', 'integer', 'min:0'],
            'timezone' => ['required', 'string', 'max:64'],
            'recurrence_rule' => ['nullable', 'array'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messagesFor(): array
    {
        return [
            'product_id.required' => 'A product is required for this series.',
            'product_id.uuid' => 'Product IDs must be valid UUIDs.',
            'product_id.exists' => 'Product must belong to the current partner.',
            'name.required' => 'A series name is required.',
            'name.max' => 'Series names may not exceed 150 characters.',
            'starts_at.required' => 'A start time is required.',
            'starts_at.date_format' => 'Start time must be in HH:MM format.',
            'ends_at.required' => 'An end time is required.',
            'ends_at.date_format' => 'End time must be in HH:MM format.',
            'ends_at.after' => 'End time must be after the start time.',
            'capacity_total.integer' => 'Total capacity must be a whole number.',
            'capacity_total.min' => 'Total capacity cannot be negative.',
            'timezone.required' => 'A timezone is required.',
            'timezone.max' => 'Timezone values may not exceed 64 characters.',
            'recurrence_rule.array' => 'Recurrence rules must be an array.',
            'status.required' => 'A status is required.',
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
        $partner = $this->attributes->get('currentPartner');
        $series = $this->route('eventSeries');

        if (is_string($series)) {
            $series = EventSeries::query()
                ->when($partner?->id, fn ($query) => $query->where('partner_id', $partner->id))
                ->find($series);
        }

        if ($partner?->id && $series instanceof EventSeries) {
            return self::rulesFor($partner->id);
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
