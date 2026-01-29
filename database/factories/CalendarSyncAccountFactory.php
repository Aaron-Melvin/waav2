<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CalendarSyncAccount>
 */
class CalendarSyncAccountFactory extends Factory
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
            'provider' => fake()->randomElement(['google', 'outlook', 'ical']),
            'external_id' => fake()->uuid(),
            'email' => fake()->safeEmail(),
            'status' => 'active',
            'access_token' => fake()->sha1(),
            'refresh_token' => fake()->sha1(),
            'token_expires_at' => now()->addDays(30),
            'meta' => [
                'sync_direction' => fake()->randomElement(['pull', 'push']),
            ],
        ];
    }
}
