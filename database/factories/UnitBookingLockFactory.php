<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Unit;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UnitBookingLock>
 */
class UnitBookingLockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = CarbonImmutable::now()->addDays(fake()->numberBetween(0, 30))->toDateString();

        return [
            'booking_id' => Booking::factory(),
            'unit_id' => Unit::factory(),
            'date' => $date,
        ];
    }
}
