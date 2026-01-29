<?php

namespace App\Http\Requests\Partner;

use App\Models\Partner;
use App\Models\Product;
use App\Models\RatePlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRatePlanRequest extends FormRequest
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
    public static function rulesFor(?Partner $partner, Product $product, RatePlan $ratePlan): array
    {
        $partnerId = $partner?->id;

        $codeRule = Rule::unique('rate_plans', 'code')
            ->where(function ($query) use ($partnerId, $product): void {
                if ($partnerId) {
                    $query->where('partner_id', $partnerId);
                }

                $query->where('product_id', $product->id);
            })
            ->ignore($ratePlan->id);

        return [
            'name' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:50', $codeRule],
            'pricing_model' => ['required', 'string', Rule::in(['per_night', 'per_person'])],
            'currency' => ['required', 'string', 'size:3'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
            'cancellation_policy_id' => [
                'nullable',
                'uuid',
                Rule::exists('cancellation_policies', 'id')->where(function ($query) use ($partnerId): void {
                    if ($partnerId) {
                        $query->where('partner_id', $partnerId);
                    }
                }),
            ],
        ];
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public static function rulesForCreate(?Partner $partner, Product $product): array
    {
        $ratePlan = new RatePlan();

        return self::rulesFor($partner, $product, $ratePlan);
    }

    /**
     * @return array<string, string>
     */
    public static function messagesFor(): array
    {
        return [
            'name.required' => 'A rate plan name is required.',
            'name.max' => 'Rate plan names may not exceed 150 characters.',
            'code.max' => 'Rate plan codes may not exceed 50 characters.',
            'code.unique' => 'That code is already in use for this product.',
            'pricing_model.required' => 'A pricing model is required.',
            'pricing_model.in' => 'Pricing model must be per night or per person.',
            'currency.required' => 'A currency is required.',
            'currency.size' => 'Currency codes must be 3 characters.',
            'status.required' => 'A status is required.',
            'status.in' => 'Status must be active or inactive.',
            'cancellation_policy_id.uuid' => 'Cancellation policy IDs must be valid UUIDs.',
            'cancellation_policy_id.exists' => 'Cancellation policy must belong to the current partner.',
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
        $ratePlan = $this->route('ratePlan');

        if (is_string($product) && $partner instanceof Partner) {
            $product = Product::query()
                ->where('partner_id', $partner->id)
                ->find($product);
        }

        if (is_string($ratePlan) && $product instanceof Product) {
            $ratePlan = RatePlan::query()
                ->where('product_id', $product->id)
                ->find($ratePlan);
        }

        if ($partner instanceof Partner && $product instanceof Product && $ratePlan instanceof RatePlan) {
            return self::rulesFor($partner, $product, $ratePlan);
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
