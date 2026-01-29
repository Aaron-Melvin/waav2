<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Refund>
 */
class RefundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_id' => Payment::factory(),
            'amount' => fake()->randomFloat(2, 10, 120),
            'currency' => 'EUR',
            'status' => fake()->randomElement(['pending', 'succeeded', 'failed']),
            'provider_refund_id' => Str::upper(fake()->bothify('RFND-######')),
            'reason' => fake()->randomElement(['customer_request', 'overbooked', 'weather']),
            'raw_payload' => [
                'note' => fake()->sentence(),
            ],
        ];
    }
}
