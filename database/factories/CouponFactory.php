<?php

namespace Database\Factories;

use App\Models\Partner;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = CarbonImmutable::today()->subDays(fake()->numberBetween(1, 10));
        $end = $start->addDays(fake()->numberBetween(15, 60));

        return [
            'partner_id' => Partner::factory(),
            'code' => Str::upper(fake()->bothify('WAA##??')),
            'name' => fake()->randomElement(['Welcome', 'Early Bird', 'Loyalty']),
            'description' => fake()->sentence(),
            'discount_type' => fake()->randomElement(['percent', 'fixed']),
            'discount_value' => fake()->randomElement([10, 15, 20, 25]),
            'max_redemptions' => fake()->numberBetween(25, 200),
            'max_per_customer' => fake()->numberBetween(1, 3),
            'starts_on' => $start,
            'ends_on' => $end,
            'min_total' => fake()->randomFloat(2, 50, 200),
            'status' => 'active',
            'meta' => [
                'channel' => fake()->randomElement(['email', 'partner', 'admin']),
            ],
        ];
    }
}
