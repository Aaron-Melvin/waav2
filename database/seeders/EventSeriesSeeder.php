<?php

namespace Database\Seeders;

use App\Models\EventSeries;
use App\Models\Product;
use Illuminate\Database\Seeder;

class EventSeriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::query()
            ->where('type', 'event')
            ->get();

        if ($products->isEmpty()) {
            return;
        }

        foreach ($products as $product) {
            EventSeries::factory()
                ->count(2)
                ->create([
                    'partner_id' => $product->partner_id,
                    'product_id' => $product->id,
                ]);
        }
    }
}
