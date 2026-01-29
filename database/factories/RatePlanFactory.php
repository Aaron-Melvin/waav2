<?php

namespace Database\Factories;

use App\Models\CancellationPolicy;
use App\Models\Partner;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RatePlan>
 */
class RatePlanFactory extends Factory
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
            'cancellation_policy_id' => CancellationPolicy::factory()->for($partner),
            'name' => fake()->randomElement(['Standard', 'Flexible', 'Non-refundable']),
            'code' => Str::upper(Str::random(6)),
            'pricing_model' => fake()->randomElement(['per_night', 'per_person']),
            'currency' => 'EUR',
            'status' => 'active',
            'meta' => [
                'meal_plan' => fake()->randomElement(['room_only', 'breakfast', 'half_board']),
            ],
        ];
    }
}
