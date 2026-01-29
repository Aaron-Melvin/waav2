<?php

namespace Database\Factories;

use App\Models\Webhook;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WebhookDelivery>
 */
class WebhookDeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'webhook_id' => Webhook::factory(),
            'event' => fake()->randomElement(['booking.confirmed', 'booking.cancelled']),
            'payload' => [
                'booking_reference' => fake()->bothify('WAA-####'),
            ],
            'status' => fake()->randomElement(['pending', 'delivered', 'failed']),
            'attempt_count' => fake()->numberBetween(0, 3),
            'last_error' => null,
            'response_code' => fake()->randomElement([200, 202, 500]),
            'response_body' => fake()->boolean(30) ? fake()->sentence() : null,
            'delivered_at' => CarbonImmutable::now()->subMinutes(fake()->numberBetween(1, 30)),
            'next_retry_at' => null,
        ];
    }
}
