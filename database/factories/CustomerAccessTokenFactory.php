<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Partner;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerAccessToken>
 */
class CustomerAccessTokenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partner = Partner::factory();

        return [
            'partner_id' => $partner,
            'customer_id' => Customer::factory()->for($partner),
            'token' => Str::random(48),
            'purpose' => fake()->randomElement(['magic-link', 'account-recovery']),
            'expires_at' => CarbonImmutable::now()->addHours(4),
            'used_at' => null,
            'meta' => [
                'ip' => fake()->ipv4(),
            ],
        ];
    }
}
