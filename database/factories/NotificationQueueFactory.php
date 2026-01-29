<?php

namespace Database\Factories;

use App\Models\NotificationTemplate;
use App\Models\Partner;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationQueue>
 */
class NotificationQueueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partner = Partner::factory();
        $channel = fake()->randomElement(['email', 'sms']);

        return [
            'partner_id' => $partner,
            'notification_template_id' => NotificationTemplate::factory()->for($partner),
            'channel' => $channel,
            'recipient' => $channel === 'email' ? fake()->safeEmail() : fake()->e164PhoneNumber(),
            'status' => fake()->randomElement(['pending', 'processing', 'failed']),
            'attempts' => fake()->numberBetween(0, 3),
            'last_error' => fake()->boolean(20) ? fake()->sentence() : null,
            'model_type' => null,
            'model_id' => null,
            'scheduled_for' => CarbonImmutable::now()->addMinutes(fake()->numberBetween(5, 120)),
            'payload' => [
                'booking_reference' => fake()->bothify('WAA-####'),
            ],
            'meta' => [
                'priority' => fake()->randomElement(['low', 'normal', 'high']),
            ],
        ];
    }
}
