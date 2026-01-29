<?php

namespace App\Http\Requests\Admin;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRefundRequest extends FormRequest
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
        /** @var Payment|null $payment */
        $payment = $this->route('payment');

        $amountRules = ['required', 'numeric', 'min:0.01'];

        if ($payment) {
            $amountRules[] = 'max:'.$payment->amount;
        }

        return [
            'amount' => $amountRules,
            'status' => ['nullable', 'string', Rule::in(['pending', 'succeeded', 'failed'])],
            'reason' => ['nullable', 'string', 'max:255'],
            'provider_refund_id' => ['nullable', 'string', 'max:150'],
            'raw_payload' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'A refund amount is required.',
            'amount.min' => 'Refund amounts must be greater than zero.',
            'amount.max' => 'Refund amounts may not exceed the payment amount.',
            'status.in' => 'Status must be pending, succeeded, or failed.',
            'reason.max' => 'Reasons may not exceed 255 characters.',
        ];
    }
}
