<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryLedger>
 */
class InventoryLedgerFactory extends Factory
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

        return [
            'partner_id' => $partner,
            'product_id' => $product,
            'event_id' => null,
            'unit_id' => null,
            'hold_id' => null,
            'delta' => fake()->randomElement([-3, -2, -1, 1, 2, 3]),
            'reason' => fake()->randomElement(['hold', 'release', 'booking', 'adjustment']),
            'meta' => [
                'source' => fake()->randomElement(['system', 'admin', 'partner']),
            ],
        ];
    }
}
