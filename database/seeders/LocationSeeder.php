<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Partner;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
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
            Location::factory()
                ->count(5)
                ->for($partner)
                ->create();
        }
    }
}
