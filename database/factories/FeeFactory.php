<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fee>
 */
class FeeFactory extends Factory
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
            'name' => fake()->randomElement(['Service Fee', 'Cleaning Fee', 'Processing Fee']),
            'type' => fake()->randomElement(['flat', 'per_night', 'per_person']),
            'amount' => fake()->randomFloat(2, 2, 35),
            'applies_to' => fake()->randomElement(['booking', 'accommodation', 'event']),
            'status' => 'active',
            'meta' => [
                'code' => fake()->bothify('FEE-###'),
            ],
        ];
    }
}
