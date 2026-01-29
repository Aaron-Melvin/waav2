<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\Unit;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UnitCalendar>
 */
class UnitCalendarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = CarbonImmutable::now()->addDays(fake()->numberBetween(0, 30))->toDateString();
        $partner = Partner::factory();

        return [
            'partner_id' => $partner,
            'unit_id' => Unit::factory()->for($partner),
            'date' => $date,
            'is_available' => fake()->boolean(80),
            'min_stay_nights' => fake()->randomElement([null, 1, 2]),
            'max_stay_nights' => fake()->randomElement([null, 5, 7, 10]),
            'reason' => fake()->boolean(15) ? fake()->sentence(3) : null,
        ];
    }
}
