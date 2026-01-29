<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Partner;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = Partner::query()->get();

        if ($partners->isEmpty()) {
            $partners = Partner::factory()->count(2)->create();
        }

        foreach ($partners as $partner) {
            $locations = Location::query()
                ->where('partner_id', $partner->id)
                ->get();

            if ($locations->isEmpty()) {
                $locations = Location::factory()->count(3)->for($partner)->create();
            }

            $eventCount = 14;
            $accommodationCount = 6;

            Product::factory()
                ->count($eventCount)
                ->for($partner)
                ->state(fn () => [
                    'location_id' => $locations->random()->id,
                ])
                ->create();

            Product::factory()
                ->count($accommodationCount)
                ->accommodation()
                ->for($partner)
                ->state(fn () => [
                    'location_id' => $locations->random()->id,
                ])
                ->create();
        }
    }
}
