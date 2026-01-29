<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::query()
            ->where('type', 'accommodation')
            ->get();

        if ($products->isEmpty()) {
            return;
        }

        foreach ($products as $product) {
            Unit::factory()
                ->count(5)
                ->create([
                    'partner_id' => $product->partner_id,
                    'product_id' => $product->id,
                ]);
        }
    }
}
