<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingAllocation;
use App\Models\Event;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class BookingAllocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = Booking::query()->get();

        if ($bookings->isEmpty()) {
            return;
        }

        foreach ($bookings as $booking) {
            $events = Event::query()->where('partner_id', $booking->partner_id)->get();
            $units = Unit::query()->where('partner_id', $booking->partner_id)->get();

            if ($events->isNotEmpty()) {
                BookingAllocation::factory()->create([
                    'booking_id' => $booking->id,
                    'event_id' => $events->random()->id,
                    'unit_id' => null,
                ]);
            } elseif ($units->isNotEmpty()) {
                BookingAllocation::factory()->create([
                    'booking_id' => $booking->id,
                    'event_id' => null,
                    'unit_id' => $units->random()->id,
                ]);
            }
        }
    }
}
