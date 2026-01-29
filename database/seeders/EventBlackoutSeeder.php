<?php

namespace Database\Seeders;

use App\Models\EventBlackout;
use App\Models\Location;
use App\Models\Partner;
use App\Models\Product;
use Illuminate\Database\Seeder;

class EventBlackoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = Partner::query()->get();

        if ($partners->isEmpty()) {
            return;
        }

        foreach ($partners as $partner) {
            $locations = Location::query()
                ->where('partner_id', $partner->id)
                ->get();
            $products = Product::query()
                ->where('partner_id', $partner->id)
                ->where('type', 'event')
                ->get();

            if ($locations->isEmpty() || $products->isEmpty()) {
                continue;
            }

            for ($i = 0; $i < 2; $i++) {
                EventBlackout::factory()->create([
                    'partner_id' => $partner->id,
                    'location_id' => $locations->random()->id,
                    'product_id' => $products->random()->id,
                ]);
            }
        }
    }
}
