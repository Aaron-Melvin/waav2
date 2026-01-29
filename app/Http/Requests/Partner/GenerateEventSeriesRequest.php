<?php

namespace App\Http\Requests\Partner;

use App\Models\EventSeries;
use Illuminate\Foundation\Http\FormRequest;

class GenerateEventSeriesRequest extends FormRequest
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
        $series = $this->route('eventSeries');

        if (is_string($series)) {
            $partner = $this->attributes->get('currentPartner');
            $series = EventSeries::query()
                ->when($partner?->id, fn ($query) => $query->where('partner_id', $partner->id))
                ->find($series);
        }

        if ($series instanceof EventSeries) {
            return [
                'date_range' => ['required', 'array'],
                'date_range.from' => ['required', 'date'],
                'date_range.to' => ['required', 'date', 'after_or_equal:date_range.from'],
                'preview' => ['nullable', 'boolean'],
            ];
        }

        return [];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date_range.required' => 'A date range is required.',
            'date_range.array' => 'Date range must be an object.',
            'date_range.from.required' => 'A start date is required.',
            'date_range.from.date' => 'Start date must be valid.',
            'date_range.to.required' => 'An end date is required.',
            'date_range.to.date' => 'End date must be valid.',
            'date_range.to.after_or_equal' => 'End date must be on or after the start date.',
            'preview.boolean' => 'Preview must be true or false.',
        ];
    }
}
