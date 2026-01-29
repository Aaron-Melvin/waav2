<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EligibilityRule>
 */
class EligibilityRuleFactory extends Factory
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
            'product_id' => Product::factory()->for($partner),
            'name' => fake()->randomElement(['Age Limit', 'Membership Required', 'Height Requirement']),
            'kind' => fake()->randomElement(['age_minimum', 'membership', 'height_minimum']),
            'config' => [
                'min_age' => fake()->numberBetween(12, 21),
                'membership_level' => fake()->randomElement(['silver', 'gold', 'platinum']),
                'min_height_cm' => fake()->numberBetween(140, 170),
            ],
            'status' => 'active',
        ];
    }
}
