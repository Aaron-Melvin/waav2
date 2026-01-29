<?php

namespace App\Http\Requests\Partner;

use App\Models\Product;
use App\Models\Unit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
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
    public static function rulesFor(Product $product, Unit $unit): array
    {
        $codeRule = Rule::unique('units', 'code')
            ->where(function ($query) use ($product): void {
                $query->where('product_id', $product->id);
            })
            ->ignore($unit->id);

        return [
            'name' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:50', $codeRule],
            'occupancy_adults' => ['required', 'integer', 'min:1'],
            'occupancy_children' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
            'housekeeping_required' => ['required', 'boolean'],
            'meta' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public static function rulesForCreate(Product $product): array
    {
        $unit = new Unit();

        return self::rulesFor($product, $unit);
    }

    /**
     * @return array<string, string>
     */
    public static function messagesFor(): array
    {
        return [
            'name.required' => 'A unit name is required.',
            'name.max' => 'Unit names may not exceed 150 characters.',
            'code.max' => 'Unit codes may not exceed 50 characters.',
            'code.unique' => 'That unit code is already in use for this product.',
            'occupancy_adults.required' => 'Adult occupancy is required.',
            'occupancy_adults.integer' => 'Adult occupancy must be a whole number.',
            'occupancy_adults.min' => 'Adult occupancy must be at least 1.',
            'occupancy_children.required' => 'Child occupancy is required.',
            'occupancy_children.integer' => 'Child occupancy must be a whole number.',
            'occupancy_children.min' => 'Child occupancy cannot be negative.',
            'status.required' => 'A unit status is required.',
            'status.in' => 'Unit status must be active or inactive.',
            'housekeeping_required.required' => 'Housekeeping status is required.',
            'housekeeping_required.boolean' => 'Housekeeping status must be true or false.',
            'meta.array' => 'Unit metadata must be a JSON object.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product = $this->route('product');
        $unit = $this->route('unit');

        if (is_string($product)) {
            $partner = $this->attributes->get('currentPartner');
            $product = Product::query()
                ->when($partner?->id, fn ($query) => $query->where('partner_id', $partner->id))
                ->find($product);
        }

        if (is_string($unit) && $product instanceof Product) {
            $unit = Unit::query()
                ->where('product_id', $product->id)
                ->find($unit);
        }

        if ($product instanceof Product && $unit instanceof Unit) {
            return self::rulesFor($product, $unit);
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
