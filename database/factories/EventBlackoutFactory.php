<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Partner;
use App\Models\Product;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventBlackout>
 */
class EventBlackoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = CarbonImmutable::now()->addDays(fake()->numberBetween(1, 30));
        $end = $start->addDays(fake()->numberBetween(1, 5));
        $partner = Partner::factory();

        return [
            'partner_id' => $partner,
            'product_id' => Product::factory()->for($partner),
            'location_id' => Location::factory()->for($partner),
            'starts_at' => $start->toDateString(),
            'ends_at' => $end->toDateString(),
            'reason' => fake()->randomElement([
                'Atlantic swell warning',
                'Trail maintenance',
                'Bank holiday closure',
                'Weather advisory',
                'Local festival',
            ]),
            'status' => 'active',
        ];
    }
}
