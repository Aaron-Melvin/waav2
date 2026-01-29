<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CancellationPolicy>
 */
class CancellationPolicyFactory extends Factory
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
            'name' => fake()->randomElement(['Flexible', 'Moderate', 'Strict']),
            'description' => fake()->sentence(),
            'rules' => [
                'window_hours' => fake()->randomElement([24, 48, 72]),
                'penalties' => [
                    ['hours_before' => 72, 'fee_percent' => 0],
                    ['hours_before' => 24, 'fee_percent' => 25],
                    ['hours_before' => 0, 'fee_percent' => 100],
                ],
            ],
            'status' => 'active',
        ];
    }
}
