<?php

namespace App\Http\Requests\Partner;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
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
    public static function rulesFor(Product $product): array
    {
        return UpdateUnitRequest::rulesForCreate($product);
    }

    /**
     * @return array<string, string>
     */
    public static function messagesFor(): array
    {
        return UpdateUnitRequest::messagesFor();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product');

        if (is_string($productId)) {
            $partner = $this->attributes->get('currentPartner');
            $product = Product::query()
                ->when($partner?->id, fn ($query) => $query->where('partner_id', $partner->id))
                ->find($productId);

            if ($product instanceof Product) {
                return self::rulesFor($product);
            }
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
