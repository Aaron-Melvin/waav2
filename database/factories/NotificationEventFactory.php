<?php

namespace Database\Factories;

use App\Models\NotificationTemplate;
use App\Models\Partner;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationEvent>
 */
class NotificationEventFactory extends Factory
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
            'notification_template_id' => NotificationTemplate::factory()->for($partner),
            'event' => fake()->randomElement(['booking.confirmed', 'booking.cancelled', 'booking.reminder']),
            'channel' => fake()->randomElement(['email', 'sms']),
            'recipient' => fake()->safeEmail(),
            'status' => fake()->randomElement(['queued', 'sent', 'failed']),
            'payload' => [
                'booking_reference' => fake()->bothify('WAA-####'),
            ],
            'meta' => [
                'provider' => fake()->randomElement(['ses', 'twilio']),
            ],
            'sent_at' => CarbonImmutable::now()->subMinutes(fake()->numberBetween(5, 120)),
        ];
    }
}
