<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Partner;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partner = Partner::factory();
        $status = fake()->randomElement(['pending', 'authorized', 'captured', 'failed']);

        return [
            'partner_id' => $partner,
            'booking_id' => Booking::factory()->for($partner),
            'provider' => fake()->randomElement(['stripe', 'adyen', 'manual']),
            'provider_payment_id' => Str::upper(fake()->bothify('PAY-########')),
            'amount' => fake()->randomFloat(2, 80, 420),
            'currency' => 'EUR',
            'status' => $status,
            'captured_at' => $status === 'captured' ? CarbonImmutable::now()->subMinutes(10) : null,
            'raw_payload' => [
                'source' => fake()->randomElement(['card', 'bank_transfer']),
            ],
            'meta' => [
                'channel' => fake()->randomElement(['api', 'admin', 'front']),
            ],
        ];
    }
}
