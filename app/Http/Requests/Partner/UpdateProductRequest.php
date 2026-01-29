<?php

namespace App\Http\Requests\Partner;

use App\Models\Partner;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
    public static function rulesFor(?Partner $partner, Product $product): array
    {
        $partnerId = $partner?->id;
        $type = $product->type;

        $slugRule = Rule::unique('products', 'slug')->where(function ($query) use ($partnerId, $type): void {
            if ($partnerId) {
                $query->where('partner_id', $partnerId);
            }

            if ($type) {
                $query->where('type', $type);
            }
        })->ignore($product->id);

        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', $slugRule],
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
            'slug.required' => 'A product slug is required.',
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
        $partner = $this->attributes->get('currentPartner');
        $product = $this->route('product');

        if (is_string($product) && $partner) {
            $product = Product::query()
                ->where('partner_id', $partner->id)
                ->find($product);
        }

        if ($product instanceof Product) {
            return self::rulesFor($partner, $product);
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
