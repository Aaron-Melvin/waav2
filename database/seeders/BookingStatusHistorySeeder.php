<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingStatusHistory;
use Illuminate\Database\Seeder;

class BookingStatusHistorySeeder extends Seeder
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
            BookingStatusHistory::factory()
                ->count(2)
                ->create([
                    'booking_id' => $booking->id,
                ]);
        }
    }
}
