<?php

namespace App\Http\Requests\Partner;

use App\Models\Coupon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCouponRequest extends FormRequest
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
    public static function rulesFor(string $partnerId, Coupon $coupon): array
    {
        $codeRule = Rule::unique('coupons', 'code')
            ->where(function ($query) use ($partnerId): void {
                $query->where('partner_id', $partnerId);
            })
            ->ignore($coupon->id);

        return [
            'code' => ['required', 'string', 'max:50', $codeRule],
            'name' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'discount_type' => ['required', 'string', Rule::in(['percent', 'fixed'])],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'max_redemptions' => ['nullable', 'integer', 'min:1'],
            'max_per_customer' => ['nullable', 'integer', 'min:1'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'min_total' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
            'meta' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messagesFor(): array
    {
        return [
            'code.required' => 'A coupon code is required.',
            'code.max' => 'Coupon codes may not exceed 50 characters.',
            'code.unique' => 'Coupon codes must be unique per partner.',
            'name.max' => 'Coupon names may not exceed 150 characters.',
            'discount_type.required' => 'A discount type is required.',
            'discount_type.in' => 'Discount type must be percent or fixed.',
            'discount_value.required' => 'A discount value is required.',
            'discount_value.numeric' => 'Discount value must be a number.',
            'discount_value.min' => 'Discount value cannot be negative.',
            'max_redemptions.integer' => 'Max redemptions must be a whole number.',
            'max_redemptions.min' => 'Max redemptions must be at least 1.',
            'max_per_customer.integer' => 'Max per customer must be a whole number.',
            'max_per_customer.min' => 'Max per customer must be at least 1.',
            'ends_on.after_or_equal' => 'End date must be on or after the start date.',
            'min_total.numeric' => 'Minimum total must be a number.',
            'min_total.min' => 'Minimum total cannot be negative.',
            'status.required' => 'A status is required.',
            'status.in' => 'Status must be active or inactive.',
            'meta.array' => 'Meta must be an object.',
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
        $coupon = $this->route('coupon');

        if (is_string($coupon)) {
            $coupon = Coupon::query()
                ->when($partner?->id, fn ($query) => $query->where('partner_id', $partner->id))
                ->find($coupon);
        }

        if ($partner?->id && $coupon instanceof Coupon) {
            return self::rulesFor($partner->id, $coupon);
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
