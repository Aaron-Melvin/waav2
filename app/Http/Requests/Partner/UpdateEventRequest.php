<?php

namespace App\Http\Requests\Partner;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends FormRequest
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
            'status' => ['required', 'string', Rule::in(['scheduled', 'cancelled', 'completed'])],
            'publish_state' => ['required', 'string', Rule::in(['draft', 'published'])],
            'traffic_light' => ['nullable', 'string', Rule::in(['green', 'yellow', 'red'])],
            'capacity_total' => ['nullable', 'integer', 'min:0'],
            'capacity_reserved' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messagesFor(): array
    {
        return [
            'status.required' => 'An event status is required.',
            'status.in' => 'Status must be scheduled, cancelled, or completed.',
            'publish_state.required' => 'A publish state is required.',
            'publish_state.in' => 'Publish state must be draft or published.',
            'traffic_light.in' => 'Traffic light must be green, yellow, or red.',
            'capacity_total.integer' => 'Total capacity must be a whole number.',
            'capacity_total.min' => 'Total capacity cannot be negative.',
            'capacity_reserved.integer' => 'Reserved capacity must be a whole number.',
            'capacity_reserved.min' => 'Reserved capacity cannot be negative.',
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
