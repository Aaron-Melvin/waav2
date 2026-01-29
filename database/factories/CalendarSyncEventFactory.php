<?php

namespace Database\Factories;

use App\Models\CalendarSyncAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CalendarSyncEvent>
 */
class CalendarSyncEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'calendar_sync_account_id' => CalendarSyncAccount::factory(),
            'product_id' => null,
            'unit_id' => null,
            'event_id' => null,
            'external_event_id' => fake()->uuid(),
            'direction' => fake()->randomElement(['push', 'pull']),
            'status' => 'active',
            'last_synced_at' => now()->subHours(fake()->numberBetween(1, 48)),
            'meta' => [
                'external_calendar' => fake()->word(),
            ],
        ];
    }
}
