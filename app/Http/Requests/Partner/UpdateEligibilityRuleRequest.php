<?php

namespace App\Http\Requests\Partner;

use App\Models\EligibilityRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEligibilityRuleRequest extends FormRequest
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
    public static function rulesFor(string $partnerId): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'kind' => ['required', 'string', 'max:100'],
            'config' => ['required', 'array'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
            'product_id' => [
                'nullable',
                'uuid',
                Rule::exists('products', 'id')->where(function ($query) use ($partnerId): void {
                    $query->where('partner_id', $partnerId);
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
            'name.required' => 'A rule name is required.',
            'name.max' => 'Rule names may not exceed 150 characters.',
            'kind.required' => 'A rule kind is required.',
            'kind.max' => 'Rule kinds may not exceed 100 characters.',
            'config.required' => 'Rule configuration is required.',
            'config.array' => 'Rule configuration must be an object.',
            'status.required' => 'A status is required.',
            'status.in' => 'Status must be active or inactive.',
            'product_id.uuid' => 'Product IDs must be valid UUIDs.',
            'product_id.exists' => 'Product must belong to the current partner.',
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
        $rule = $this->route('eligibilityRule');

        if (is_string($rule)) {
            $rule = EligibilityRule::query()
                ->when($partner?->id, fn ($query) => $query->where('partner_id', $partner->id))
                ->find($rule);
        }

        if ($partner?->id && $rule instanceof EligibilityRule) {
            return self::rulesFor($partner->id);
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
