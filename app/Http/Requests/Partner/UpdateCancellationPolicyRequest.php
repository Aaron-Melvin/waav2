<?php

namespace App\Http\Requests\Partner;

use App\Models\CancellationPolicy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCancellationPolicyRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'rules' => ['required', 'array'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messagesFor(): array
    {
        return [
            'name.required' => 'A policy name is required.',
            'name.max' => 'Policy names may not exceed 150 characters.',
            'rules.required' => 'Cancellation rules are required.',
            'rules.array' => 'Cancellation rules must be an array.',
            'status.required' => 'A status is required.',
            'status.in' => 'Status must be active or inactive.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $policy = $this->route('cancellationPolicy');

        if (is_string($policy)) {
            $partner = $this->attributes->get('currentPartner');
            $policy = CancellationPolicy::query()
                ->when($partner?->id, fn ($query) => $query->where('partner_id', $partner->id))
                ->find($policy);
        }

        if ($policy instanceof CancellationPolicy) {
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
