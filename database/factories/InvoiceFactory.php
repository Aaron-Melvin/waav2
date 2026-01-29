<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Partner;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partner = Partner::factory();
        $issued = CarbonImmutable::now()->subDays(fake()->numberBetween(1, 14));

        return [
            'partner_id' => $partner,
            'booking_id' => Booking::factory()->for($partner),
            'number' => Str::upper(fake()->bothify('INV-######')),
            'currency' => 'EUR',
            'total_gross' => fake()->randomFloat(2, 80, 420),
            'total_tax' => fake()->randomFloat(2, 5, 40),
            'total_fees' => fake()->randomFloat(2, 0, 20),
            'status' => fake()->randomElement(['issued', 'paid', 'void']),
            'issued_at' => $issued,
            'due_at' => $issued->addDays(14),
            'pdf_url' => fake()->url(),
            'meta' => [
                'billing_contact' => fake()->email(),
            ],
        ];
    }
}
