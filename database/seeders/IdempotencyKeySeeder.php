<?php

namespace Database\Seeders;

use App\Models\IdempotencyKey;
use App\Models\Partner;
use Illuminate\Database\Seeder;

class IdempotencyKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = Partner::query()->get();

        if ($partners->isEmpty()) {
            return;
        }

        foreach ($partners as $partner) {
            IdempotencyKey::factory()
                ->count(5)
                ->create([
                    'partner_id' => $partner->id,
                ]);
        }
    }
}
