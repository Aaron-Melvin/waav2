<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventSeries;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $series = EventSeries::query()->get();

        if ($series->isEmpty()) {
            return;
        }

        foreach ($series as $item) {
            $timezone = $item->timezone ?: config('app.timezone');

            for ($i = 0; $i < 6; $i++) {
                $start = CarbonImmutable::now($timezone)
                    ->addDays(1 + ($i * 3))
                    ->setTimeFromTimeString($item->starts_at);

                $end = $start->setTimeFromTimeString($item->ends_at);

                if ($end->lessThanOrEqualTo($start)) {
                    $end = $start->addHours(2);
                }

                Event::factory()->create([
                    'partner_id' => $item->partner_id,
                    'product_id' => $item->product_id,
                    'event_series_id' => $item->id,
                    'starts_at' => $start,
                    'ends_at' => $end,
                    'capacity_total' => $item->capacity_total,
                    'capacity_reserved' => fake()->numberBetween(0, 6),
                    'status' => 'scheduled',
                    'publish_state' => 'published',
                    'weather_alert' => fake()->boolean(5),
                ]);
            }
        }
    }
}
