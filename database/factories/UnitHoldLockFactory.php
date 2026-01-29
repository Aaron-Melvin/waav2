<?php

namespace Database\Factories;

use App\Models\Hold;
use App\Models\Unit;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UnitHoldLock>
 */
class UnitHoldLockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = CarbonImmutable::now()->addDays(fake()->numberBetween(0, 30))->toDateString();
        $unit = Unit::factory();
        $hold = Hold::factory()->state([
            'unit_id' => null,
        ]);

        return [
            'hold_id' => $hold,
            'unit_id' => $unit,
            'date' => $date,
        ];
    }
}
