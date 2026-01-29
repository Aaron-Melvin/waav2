<?php

namespace Database\Factories;

use App\Models\RatePlan;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RatePlanPrice>
 */
class RatePlanPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = CarbonImmutable::today()->addDays(fake()->numberBetween(1, 30));
        $end = $start->addDays(fake()->numberBetween(2, 7));

        return [
            'rate_plan_id' => RatePlan::factory(),
            'starts_on' => $start,
            'ends_on' => $end,
            'price' => fake()->randomFloat(2, 80, 240),
            'extra_adult' => fake()->randomFloat(2, 10, 40),
            'extra_child' => fake()->randomFloat(2, 5, 25),
            'restrictions' => [
                'min_stay' => fake()->numberBetween(1, 3),
                'max_stay' => fake()->numberBetween(3, 14),
            ],
        ];
    }
}
