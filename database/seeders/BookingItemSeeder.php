<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Event;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class BookingItemSeeder extends Seeder
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
            $products = Product::query()
                ->where('partner_id', $booking->partner_id)
                ->get();
            $events = Event::query()
                ->where('partner_id', $booking->partner_id)
                ->get();
            $units = Unit::query()
                ->where('partner_id', $booking->partner_id)
                ->get();

            $itemType = $events->isNotEmpty() ? 'event' : 'accommodation';

            if ($itemType === 'event') {
                $event = $events->random();

                BookingItem::factory()->create([
                    'booking_id' => $booking->id,
                    'product_id' => $event->product_id,
                    'event_id' => $event->id,
                    'unit_id' => null,
                    'item_type' => 'event',
                    'starts_on' => $event->starts_at?->toDateString(),
                    'ends_on' => $event->ends_at?->toDateString(),
                ]);
            } elseif ($units->isNotEmpty()) {
                $unit = $units->random();

                BookingItem::factory()->create([
                    'booking_id' => $booking->id,
                    'product_id' => $unit->product_id,
                    'event_id' => null,
                    'unit_id' => $unit->id,
                    'item_type' => 'accommodation',
                ]);
            } elseif ($products->isNotEmpty()) {
                $product = $products->random();

                BookingItem::factory()->create([
                    'booking_id' => $booking->id,
                    'product_id' => $product->id,
                    'event_id' => null,
                    'unit_id' => null,
                    'item_type' => $product->type,
                ]);
            }
        }
    }
}
