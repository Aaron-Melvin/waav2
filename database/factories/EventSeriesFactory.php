<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventSeries>
 */
class EventSeriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTimes = ['09:00:00', '11:00:00', '14:00:00', '16:00:00'];
        $seriesNames = [
            'Morning Atlantic Session',
            'Coastal Adventure Session',
            'Sunset Sea Session',
            'Wicklow Trail Session',
            'Wild Atlantic Way Session',
        ];
        $start = fake()->randomElement($startTimes);
        $partner = Partner::factory();

        return [
            'partner_id' => $partner,
            'product_id' => Product::factory()->for($partner),
            'name' => fake()->randomElement($seriesNames),
            'starts_at' => $start,
            'ends_at' => fake()->randomElement(['12:00:00', '13:00:00', '15:30:00', '18:00:00']),
            'capacity_total' => fake()->numberBetween(8, 30),
            'timezone' => 'Europe/Dublin',
            'recurrence_rule' => [
                'frequency' => 'weekly',
                'interval' => 1,
                'byweekday' => fake()->randomElements(['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU'], fake()->numberBetween(2, 5)),
            ],
            'status' => 'active',
        ];
    }
}
