<?php

namespace App\Http\Requests\Partner;

use App\Models\Partner;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreRatePlanRequest extends FormRequest
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
        $partner = $this->attributes->get('currentPartner');
        $productId = $this->route('product');

        if ($partner instanceof Partner && is_string($productId)) {
            $product = Product::query()
                ->where('partner_id', $partner->id)
                ->find($productId);

            if ($product instanceof Product) {
                return UpdateRatePlanRequest::rulesForCreate($partner, $product);
            }
        }

        return [];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return UpdateRatePlanRequest::messagesFor();
    }
}
