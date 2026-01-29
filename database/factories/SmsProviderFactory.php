<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SmsProvider>
 */
class SmsProviderFactory extends Factory
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
            'name' => fake()->company(),
            'provider' => fake()->randomElement(['twilio', 'sns']),
            'credentials' => [
                'api_key' => fake()->bothify('key_########'),
                'sender_id' => fake()->bothify('WAA###'),
            ],
            'is_default' => fake()->boolean(30),
            'status' => 'active',
        ];
    }
}
