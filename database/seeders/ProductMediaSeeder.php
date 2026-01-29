<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductMedia;
use Illuminate\Database\Seeder;

class ProductMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::query()->get();

        if ($products->isEmpty()) {
            return;
        }

        foreach ($products as $product) {
            for ($i = 0; $i < 3; $i++) {
                ProductMedia::factory()
                    ->for($product)
                    ->create([
                        'sort' => $i,
                    ]);
            }
        }
    }
}
