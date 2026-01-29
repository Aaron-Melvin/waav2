<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Hold;
use App\Models\Product;
use App\Models\Unit;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class HoldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::query()->get();
        $units = Unit::query()->get();

        if ($events->isEmpty() && $units->isEmpty()) {
            return;
        }

        foreach ($events->take(10) as $event) {
            Hold::factory()->create([
                'partner_id' => $event->partner_id,
                'product_id' => $event->product_id,
                'event_id' => $event->id,
                'unit_id' => null,
                'starts_on' => $event->starts_at?->toDateString(),
                'ends_on' => $event->ends_at?->toDateString(),
            ]);
        }

        foreach ($units->take(10) as $unit) {
            $start = CarbonImmutable::now()->addDays(fake()->numberBetween(1, 10))->toDateString();
            $end = CarbonImmutable::now()->addDays(fake()->numberBetween(2, 12))->toDateString();

            Hold::factory()->create([
                'partner_id' => $unit->partner_id,
                'product_id' => $unit->product_id,
                'event_id' => null,
                'unit_id' => $unit->id,
                'starts_on' => $start,
                'ends_on' => $end,
            ]);
        }
    }
}
