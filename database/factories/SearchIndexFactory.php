<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Unit;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SearchIndex>
 */
class SearchIndexFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partner = Partner::factory();
        $product = Product::factory()->for($partner);
        $event = Event::factory()->for($partner)->for($product);
        $starts = CarbonImmutable::today()->addDays(fake()->numberBetween(1, 30));

        return [
            'partner_id' => $partner,
            'product_id' => $product,
            'event_id' => $event,
            'unit_id' => Unit::factory()->for($partner)->for($product),
            'location_id' => null,
            'starts_on' => $starts,
            'ends_on' => $starts->addDays(1),
            'capacity_total' => fake()->numberBetween(6, 20),
            'capacity_available' => fake()->numberBetween(1, 10),
            'price_min' => fake()->randomFloat(2, 50, 120),
            'price_max' => fake()->randomFloat(2, 120, 220),
            'currency' => 'EUR',
            'meta' => [
                'ranking_score' => fake()->randomFloat(2, 0.1, 1.0),
            ],
        ];
    }
}
