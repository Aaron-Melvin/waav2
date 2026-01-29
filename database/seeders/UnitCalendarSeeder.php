<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\UnitCalendar;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class UnitCalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = Unit::query()->get();

        if ($units->isEmpty()) {
            return;
        }

        foreach ($units as $unit) {
            for ($i = 0; $i < 14; $i++) {
                $date = CarbonImmutable::now()->addDays($i)->toDateString();

                UnitCalendar::factory()->create([
                    'partner_id' => $unit->partner_id,
                    'unit_id' => $unit->id,
                    'date' => $date,
                ]);
            }
        }
    }
}
