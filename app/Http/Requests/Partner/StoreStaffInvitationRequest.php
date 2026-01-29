<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStaffInvitationRequest extends FormRequest
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
        $partnerId = $partner?->id;

        return [
            'email' => [
                'required',
                'email',
                Rule::unique('staff_invitations', 'email')->where(function ($query) use ($partnerId): void {
                    if ($partnerId) {
                        $query->where('partner_id', $partnerId)->where('status', 'pending');
                    }
                }),
            ],
            'role' => ['nullable', 'string', Rule::in(['partner-admin', 'partner-staff'])],
            'expires_at' => ['nullable', 'date'],
            'message' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'An email address is required.',
            'email.email' => 'Email must be valid.',
            'email.unique' => 'There is already a pending invitation for this email.',
            'role.in' => 'Role must be partner admin or partner staff.',
            'expires_at.date' => 'Expiration must be a valid date.',
            'message.max' => 'Messages may not exceed 255 characters.',
        ];
    }
}
