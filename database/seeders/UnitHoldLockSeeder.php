<?php

namespace Database\Seeders;

use App\Models\Hold;
use App\Models\UnitHoldLock;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class UnitHoldLockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $holds = Hold::query()->whereNotNull('unit_id')->get();

        if ($holds->isEmpty()) {
            return;
        }

        foreach ($holds as $hold) {
            $start = CarbonImmutable::parse($hold->starts_on ?? now())->startOfDay();
            $end = CarbonImmutable::parse($hold->ends_on ?? $start)->startOfDay();

            for ($date = $start; $date->lessThanOrEqualTo($end); $date = $date->addDay()) {
                UnitHoldLock::query()->firstOrCreate([
                    'hold_id' => $hold->id,
                    'unit_id' => $hold->unit_id,
                    'date' => $date->toDateString(),
                ]);
            }
        }
    }
}
