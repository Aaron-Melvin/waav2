<?php

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partner = Partner::factory();
        $status = fake()->randomElement(['draft', 'pending_payment', 'confirmed', 'cancelled', 'completed']);
        $paymentStatus = match ($status) {
            'confirmed', 'completed' => 'paid',
            'pending_payment' => 'pending',
            'cancelled' => fake()->randomElement(['refunded', 'failed']),
            default => 'unpaid',
        };
        $coupon = fake()->boolean(25) ? Coupon::factory()->for($partner) : null;

        return [
            'partner_id' => $partner,
            'customer_id' => Customer::factory()->for($partner),
            'coupon_id' => $coupon,
            'status' => $status,
            'channel' => fake()->randomElement(['direct', 'partner', 'admin']),
            'currency' => 'EUR',
            'total_gross' => fake()->randomFloat(2, 80, 450),
            'total_tax' => fake()->randomFloat(2, 5, 40),
            'total_fees' => fake()->randomFloat(2, 0, 20),
            'payment_status' => $paymentStatus,
            'booking_reference' => Str::upper(Str::random(8)),
            'terms_version' => '2026-01',
            'meta' => [
                'source' => fake()->randomElement(['api', 'admin', 'front']),
            ],
        ];
    }
}
