<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationTemplate>
 */
class NotificationTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $channel = fake()->randomElement(['email', 'sms']);

        return [
            'partner_id' => Partner::factory(),
            'name' => fake()->randomElement(['Booking Confirmation', 'Cancellation Notice', 'Reminder']),
            'channel' => $channel,
            'locale' => 'en',
            'subject' => $channel === 'email' ? fake()->sentence() : null,
            'body' => fake()->paragraph(),
            'status' => 'active',
            'meta' => [
                'template_version' => 1,
            ],
        ];
    }
}
