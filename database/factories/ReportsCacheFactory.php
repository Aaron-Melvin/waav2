<?php

namespace Database\Factories;

use App\Models\Partner;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReportsCache>
 */
class ReportsCacheFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => Partner::factory(),
            'report_key' => fake()->slug(3),
            'payload' => [
                'total_bookings' => fake()->numberBetween(10, 150),
            ],
            'expires_at' => CarbonImmutable::now()->addHours(6),
        ];
    }
}
