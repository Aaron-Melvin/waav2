<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Unit;
use App\Models\UnitBookingLock;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class UnitBookingLockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = Booking::query()->get();
        $units = Unit::query()->get();

        if ($bookings->isEmpty() || $units->isEmpty()) {
            return;
        }

        foreach ($bookings->take(15) as $booking) {
            $unit = $units->random();
            $start = CarbonImmutable::now()->addDays(fake()->numberBetween(1, 10))->toDateString();
            $end = CarbonImmutable::now()->addDays(fake()->numberBetween(2, 12))->toDateString();

            for ($date = CarbonImmutable::parse($start); $date->lessThanOrEqualTo($end); $date = $date->addDay()) {
                UnitBookingLock::query()->firstOrCreate([
                    'booking_id' => $booking->id,
                    'unit_id' => $unit->id,
                    'date' => $date->toDateString(),
                ]);
            }
        }
    }
}
