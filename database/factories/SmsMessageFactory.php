<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Partner;
use App\Models\SmsProvider;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SmsMessage>
 */
class SmsMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partner = Partner::factory();
        $booking = Booking::factory()->for($partner);

        return [
            'partner_id' => $partner,
            'sms_provider_id' => SmsProvider::factory()->for($partner),
            'related_type' => Booking::class,
            'related_id' => $booking,
            'to' => fake()->e164PhoneNumber(),
            'from' => fake()->e164PhoneNumber(),
            'body' => fake()->sentence(),
            'status' => fake()->randomElement(['queued', 'sent', 'failed']),
            'provider_message_id' => fake()->bothify('sms_########'),
            'error_message' => null,
            'sent_at' => CarbonImmutable::now()->subMinutes(fake()->numberBetween(1, 60)),
            'payload' => [
                'booking_reference' => fake()->bothify('WAA-####'),
            ],
        ];
    }
}
