<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventOverride;
use Illuminate\Database\Seeder;

class EventOverrideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::query()->get();

        if ($events->isEmpty()) {
            return;
        }

        $events->random(min(10, $events->count()))
            ->each(function (Event $event): void {
                EventOverride::factory()->create([
                    'event_id' => $event->id,
                ]);
            });
    }
}
