<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tax>
 */
class TaxFactory extends Factory
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
            'name' => fake()->randomElement(['VAT', 'Tourism Tax', 'Service Tax']),
            'rate' => fake()->randomFloat(3, 5, 15),
            'applies_to' => fake()->randomElement(['booking', 'accommodation', 'event']),
            'is_inclusive' => fake()->boolean(30),
            'status' => 'active',
            'meta' => [
                'region' => fake()->stateAbbr(),
            ],
        ];
    }
}
