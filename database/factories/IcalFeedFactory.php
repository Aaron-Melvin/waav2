<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IcalFeed>
 */
class IcalFeedFactory extends Factory
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
            'unit_id' => null,
            'name' => fake()->randomElement(['Rooms Feed', 'Events Feed']),
            'feed_token' => Str::random(32),
            'status' => 'active',
            'last_synced_at' => null,
            'meta' => [
                'format' => 'ical',
            ],
        ];
    }
}
