<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Partner;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GdprErasureQueue>
 */
class GdprErasureQueueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partner = Partner::factory();

        return [
            'partner_id' => $partner,
            'customer_id' => Customer::factory()->for($partner),
            'status' => fake()->randomElement(['pending', 'processing', 'completed']),
            'requested_at' => CarbonImmutable::now()->subDays(fake()->numberBetween(1, 20)),
            'processed_at' => fake()->boolean(40) ? CarbonImmutable::now()->subDays(fake()->numberBetween(1, 3)) : null,
            'notes' => fake()->sentence(),
            'meta' => [
                'requested_by' => fake()->randomElement(['customer', 'support']),
            ],
        ];
    }
}
