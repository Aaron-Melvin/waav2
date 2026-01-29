<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApiClient>
 */
class ApiClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => Partner::factory(),
            'client_id' => Str::lower(Str::random(16)),
            'client_secret_hash' => Hash::make('secret-key'),
            'scopes' => ['bookings:read'],
            'status' => 'active',
        ];
    }

    public function withSecret(string $secret): static
    {
        return $this->state(fn () => [
            'client_secret_hash' => Hash::make($secret),
        ]);
    }
}
