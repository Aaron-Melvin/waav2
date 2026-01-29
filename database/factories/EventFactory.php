<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\Product;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = CarbonImmutable::now()
            ->addDays(fake()->numberBetween(1, 60))
            ->setTime(fake()->numberBetween(8, 18), fake()->randomElement([0, 15, 30, 45]));
        $durationMinutes = fake()->randomElement([60, 90, 120, 150, 180]);

        return [
            'partner_id' => Partner::factory(),
            'product_id' => Product::factory(),
            'event_series_id' => null,
            'starts_at' => $start,
            'ends_at' => $start->addMinutes($durationMinutes),
            'capacity_total' => fake()->numberBetween(8, 40),
            'capacity_reserved' => fake()->numberBetween(0, 6),
            'traffic_light' => fake()->randomElement(['green', 'yellow', 'red']),
            'status' => fake()->randomElement(['scheduled', 'cancelled', 'completed']),
            'publish_state' => fake()->randomElement(['draft', 'published']),
            'weather_alert' => fake()->boolean(10),
        ];
    }
}
