<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Partner;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
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
            Customer::factory()
                ->count(20)
                ->create([
                    'partner_id' => $partner->id,
                ]);
        }
    }
}
