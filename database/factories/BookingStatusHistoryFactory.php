<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingStatusHistory>
 */
class BookingStatusHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $from = fake()->randomElement(['draft', 'pending_payment', 'confirmed']);
        $to = fake()->randomElement(['pending_payment', 'confirmed', 'cancelled', 'completed']);

        return [
            'booking_id' => Booking::factory(),
            'from_status' => $from,
            'to_status' => $to,
            'reason' => fake()->boolean(40) ? fake()->sentence(3) : null,
            'meta' => [
                'actor' => fake()->randomElement(['system', 'customer', 'staff']),
            ],
        ];
    }
}
