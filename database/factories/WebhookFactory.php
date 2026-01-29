<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Webhook>
 */
class WebhookFactory extends Factory
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
            'name' => fake()->randomElement(['Partner Sync', 'Booking Updates', 'Payments']),
            'url' => fake()->url(),
            'events' => [
                'booking.confirmed',
                'booking.cancelled',
            ],
            'secret' => Str::random(32),
            'headers' => [
                'X-Source' => 'WAA',
            ],
            'status' => 'active',
        ];
    }
}
