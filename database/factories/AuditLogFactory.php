<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partner = Partner::factory();
        $user = User::factory()->for($partner);
        $booking = Booking::factory()->for($partner);

        return [
            'partner_id' => $partner,
            'actor_type' => User::class,
            'actor_id' => $user,
            'action' => fake()->randomElement(['booking.created', 'booking.updated', 'payment.captured']),
            'target_type' => Booking::class,
            'target_id' => $booking,
            'before' => [
                'status' => 'pending',
            ],
            'after' => [
                'status' => 'confirmed',
            ],
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'meta' => [
                'request_id' => fake()->uuid(),
            ],
        ];
    }
}
