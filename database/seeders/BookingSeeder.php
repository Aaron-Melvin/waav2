<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Partner;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
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
            $customers = Customer::query()
                ->where('partner_id', $partner->id)
                ->get();

            if ($customers->isEmpty()) {
                $customers = Customer::factory()->count(10)->create([
                    'partner_id' => $partner->id,
                ]);
            }

            Booking::factory()
                ->count(30)
                ->create([
                    'partner_id' => $partner->id,
                    'customer_id' => $customers->random()->id,
                ]);
        }
    }
}
