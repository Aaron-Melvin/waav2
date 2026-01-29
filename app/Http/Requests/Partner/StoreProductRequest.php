<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
    public static function rulesFor(?\App\Models\Partner $partner, ?string $type): array
    {
        $partnerId = $partner?->id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', Rule::in(['event', 'accommodation'])],
            'slug' => [
                'nullable',
                'string',
                'max:150',
                Rule::unique('products', 'slug')->where(function ($query) use ($partnerId, $type): void {
                    if ($partnerId) {
                        $query->where('partner_id', $partnerId);
                    }

                    if ($type) {
                        $query->where('type', $type);
                    }
                }),
            ],
            'description' => ['nullable', 'string'],
            'capacity_total' => ['nullable', 'integer', 'min:1'],
            'default_currency' => ['nullable', 'string', 'size:3'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
            'visibility' => ['nullable', 'string', Rule::in(['public', 'private', 'unlisted'])],
            'lead_time_minutes' => ['nullable', 'integer', 'min:0'],
            'cutoff_minutes' => ['nullable', 'integer', 'min:0'],
            'location_id' => [
                'nullable',
                'uuid',
                Rule::exists('locations', 'id')->where(function ($query) use ($partnerId): void {
                    if ($partnerId) {
                        $query->where('partner_id', $partnerId);
                    }
                }),
            ],
            'meta' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messagesFor(): array
    {
        return [
            'name.required' => 'A product name is required.',
            'name.max' => 'Product names may not exceed 150 characters.',
            'type.required' => 'A product type is required.',
            'type.in' => 'Product type must be event or accommodation.',
            'slug.max' => 'Slugs may not exceed 150 characters.',
            'slug.unique' => 'The slug has already been taken for this product type.',
            'capacity_total.integer' => 'Capacity must be a whole number.',
            'capacity_total.min' => 'Capacity must be at least 1.',
            'default_currency.size' => 'Currency codes must be 3 characters.',
            'status.in' => 'Status must be active or inactive.',
            'visibility.in' => 'Visibility must be public, private, or unlisted.',
            'lead_time_minutes.integer' => 'Lead time must be a whole number.',
            'cutoff_minutes.integer' => 'Cutoff time must be a whole number.',
            'location_id.uuid' => 'Location IDs must be valid UUIDs.',
            'location_id.exists' => 'Location must belong to the current partner.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $type = $this->input('type');
        $partner = $this->attributes->get('currentPartner');

        return self::rulesFor($partner, is_string($type) ? $type : null);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return self::messagesFor();
    }
}
