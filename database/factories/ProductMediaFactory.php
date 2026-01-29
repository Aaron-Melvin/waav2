<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductMedia>
 */
class ProductMediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tags = [
            'surf',
            'paddle',
            'kayak',
            'hike',
            'cliffs',
            'wild-atlantic-way',
            'irish-coast',
        ];
        $tag = fake()->randomElement($tags);
        $suffix = fake()->numberBetween(1, 200);
        $url = 'https://images.waa.test/ireland/'.Str::slug($tag).'-'.$suffix.'.jpg';

        return [
            'product_id' => Product::factory(),
            'url' => $url,
            'kind' => 'image',
            'sort' => fake()->numberBetween(0, 6),
        ];
    }
}
