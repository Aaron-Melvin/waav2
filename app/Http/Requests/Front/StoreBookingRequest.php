<?php

namespace App\Http\Requests\Front;

use App\Models\Partner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
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
        /** @var Partner|null $partner */
        $partner = $this->attributes->get('currentPartner');
        $partnerId = $partner?->id;

        return [
            'customer' => ['required', 'array'],
            'customer.name' => ['required', 'string', 'max:150'],
            'customer.email' => ['required', 'email', 'max:255'],
            'customer.phone_e164' => ['nullable', 'string', 'max:32'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_type' => ['required', 'string', Rule::in(['event', 'accommodation'])],
            'items.*.product_id' => [
                'required',
                'uuid',
                Rule::exists('products', 'id')->where('partner_id', $partnerId),
            ],
            'items.*.event_id' => [
                'nullable',
                'uuid',
                Rule::exists('events', 'id')->where('partner_id', $partnerId),
            ],
            'items.*.unit_id' => [
                'nullable',
                'uuid',
                Rule::exists('units', 'id')->where('partner_id', $partnerId),
            ],
            'items.*.starts_on' => ['nullable', 'date'],
            'items.*.ends_on' => ['nullable', 'date', 'after_or_equal:items.*.starts_on'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.total' => ['nullable', 'numeric', 'min:0'],
            'coupon_code' => [
                'nullable',
                'string',
                Rule::exists('coupons', 'code')->where('partner_id', $partnerId),
            ],
            'terms_version' => ['required', 'string', 'max:50'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $items = $this->input('items', []);

            foreach ($items as $index => $item) {
                $type = $item['item_type'] ?? null;

                if ($type === 'event' && empty($item['event_id'])) {
                    $validator->errors()->add("items.{$index}.event_id", 'Event items require an event.');
                }

                if ($type === 'accommodation' && empty($item['unit_id'])) {
                    $validator->errors()->add("items.{$index}.unit_id", 'Accommodation items require a unit.');
                }
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer.required' => 'Customer details are required.',
            'customer.name.required' => 'Customer name is required.',
            'customer.email.required' => 'Customer email is required.',
            'items.required' => 'At least one booking item is required.',
            'items.min' => 'At least one booking item is required.',
            'items.*.item_type.required' => 'Each item requires a type.',
            'items.*.item_type.in' => 'Item type must be event or accommodation.',
            'items.*.product_id.required' => 'Each item requires a product.',
            'items.*.product_id.exists' => 'The selected product is invalid.',
            'items.*.event_id.required_if' => 'Event items require an event.',
            'items.*.event_id.exists' => 'The selected event is invalid.',
            'items.*.unit_id.exists' => 'The selected unit is invalid.',
            'items.*.quantity.required' => 'Each item requires a quantity.',
            'items.*.quantity.min' => 'Quantities must be at least 1.',
            'terms_version.required' => 'You must accept the current terms to proceed.',
            'coupon_code.exists' => 'The coupon code is invalid.',
        ];
    }
}
