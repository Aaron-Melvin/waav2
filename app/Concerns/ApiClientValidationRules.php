<?php

namespace App\Concerns;

use Illuminate\Validation\Rule;

trait ApiClientValidationRules
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function apiClientRules(): array
    {
        return [
            'client_id' => ['required', 'string', 'max:64', 'unique:api_clients,client_id'],
            'client_secret' => ['nullable', 'string', 'min:16', 'max:255'],
            'scopes' => ['nullable', 'array'],
            'scopes.*' => ['string', 'max:100'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function apiClientMessages(): array
    {
        return [
            'client_id.required' => 'A client ID is required.',
            'client_id.max' => 'Client IDs may not exceed 64 characters.',
            'client_id.unique' => 'That client ID is already in use.',
            'client_secret.min' => 'Client secrets must be at least 16 characters.',
            'client_secret.max' => 'Client secrets may not exceed 255 characters.',
            'scopes.array' => 'Scopes must be an array of strings.',
            'scopes.*.string' => 'Each scope must be a string.',
            'scopes.*.max' => 'Scopes may not exceed 100 characters.',
            'status.in' => 'Status must be active or inactive.',
        ];
    }
}
