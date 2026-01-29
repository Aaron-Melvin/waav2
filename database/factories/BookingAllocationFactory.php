<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingAllocation>
 */
class BookingAllocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $booking = Booking::factory();

        return [
            'booking_id' => $booking,
            'event_id' => null,
            'unit_id' => null,
            'quantity' => fake()->numberBetween(1, 4),
        ];
    }
}
