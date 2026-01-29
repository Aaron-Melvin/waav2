<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partner = Partner::factory();
        $code = Str::upper(Str::random(4));

        return [
            'partner_id' => $partner,
            'product_id' => Product::factory()->for($partner)->state([
                'type' => 'accommodation',
            ]),
            'code' => $code,
            'name' => 'Unit '.$code,
            'occupancy_adults' => fake()->numberBetween(1, 4),
            'occupancy_children' => fake()->numberBetween(0, 3),
            'status' => 'active',
            'housekeeping_required' => fake()->boolean(20),
        ];
    }
}
