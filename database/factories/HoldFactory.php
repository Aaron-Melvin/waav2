<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Unit;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hold>
 */
class HoldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partner = Partner::factory();
        $product = Product::factory()->for($partner)->state([
            'type' => 'event',
        ]);
        $event = Event::factory()->for($partner)->state([
            'product_id' => $product,
        ]);
        $startsOn = CarbonImmutable::now()->addDays(fake()->numberBetween(1, 30))->toDateString();
        $endsOn = CarbonImmutable::now()->addDays(fake()->numberBetween(2, 35))->toDateString();

        return [
            'partner_id' => $partner,
            'product_id' => $product,
            'event_id' => $event,
            'unit_id' => null,
            'starts_on' => $startsOn,
            'ends_on' => $endsOn,
            'quantity' => fake()->numberBetween(1, 4),
            'status' => fake()->randomElement(['active', 'expired', 'released']),
            'expires_at' => now()->addMinutes(fake()->numberBetween(10, 60)),
            'meta' => [
                'source' => fake()->randomElement(['front', 'partner', 'admin']),
            ],
        ];
    }
}
