<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IdempotencyKey>
 */
class IdempotencyKeyFactory extends Factory
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
            'key' => Str::uuid()->toString(),
            'request_hash' => hash('sha256', fake()->uuid()),
            'response' => [
                'status' => 'ok',
            ],
            'status' => fake()->randomElement(['pending', 'processed', 'failed']),
            'expires_at' => now()->addMinutes(fake()->numberBetween(5, 60)),
        ];
    }
}
