<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingItem>
 */
class BookingItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $booking = Booking::factory();

        $quantity = fake()->numberBetween(1, 4);
        $unitPrice = fake()->randomFloat(2, 25, 120);

        return [
            'booking_id' => $booking,
            'product_id' => null,
            'event_id' => null,
            'unit_id' => null,
            'item_type' => fake()->randomElement(['event', 'accommodation']),
            'starts_on' => null,
            'ends_on' => null,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $unitPrice * $quantity,
            'meta' => [
                'notes' => fake()->sentence(),
            ],
        ];
    }
}
