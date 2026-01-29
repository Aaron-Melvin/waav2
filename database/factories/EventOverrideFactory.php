<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventOverride>
 */
class EventOverrideFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $field = fake()->randomElement(['capacity_total', 'notes', 'price_override']);
        $value = match ($field) {
            'capacity_total' => ['value' => fake()->numberBetween(6, 25)],
            'price_override' => ['value' => fake()->randomFloat(2, 20, 150), 'currency' => 'EUR'],
            default => ['value' => fake()->sentence()],
        };

        return [
            'event_id' => Event::factory(),
            'field' => $field,
            'value' => $value,
        ];
    }
}
