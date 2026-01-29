<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Hold;
use App\Models\InventoryLedger;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class InventoryLedgerSeeder extends Seeder
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
            $events = Event::query()->where('partner_id', $partner->id)->get();
            $units = Unit::query()->where('partner_id', $partner->id)->get();
            $holds = Hold::query()->where('partner_id', $partner->id)->get();
            $products = Product::query()->where('partner_id', $partner->id)->get();

            for ($i = 0; $i < 15; $i++) {
                InventoryLedger::factory()->create([
                    'partner_id' => $partner->id,
                    'product_id' => $products->isNotEmpty() ? $products->random()->id : null,
                    'event_id' => $events->isNotEmpty() ? $events->random()->id : null,
                    'unit_id' => $units->isNotEmpty() ? $units->random()->id : null,
                    'hold_id' => $holds->isNotEmpty() ? $holds->random()->id : null,
                ]);
            }
        }
    }
}
