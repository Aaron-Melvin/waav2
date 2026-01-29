<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StaffInvitation>
 */
class StaffInvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partner = Partner::factory();
        $firstNames = ['Liam', 'Conor', 'Sean', 'Aoife', 'Saoirse', 'Niamh', 'Orla', 'Maeve'];
        $lastNames = ["O'Brien", "O'Connor", "Murphy", "Kelly", "Walsh", "Byrne", "O'Sullivan", "Ryan"];
        $first = fake()->randomElement($firstNames);
        $last = fake()->randomElement($lastNames);
        $email = Str::slug($first.'.'.$last).fake()->numberBetween(1, 9999).'@waa.ie';

        return [
            'partner_id' => $partner,
            'inviter_id' => User::factory()->for($partner),
            'email' => $email,
            'role' => fake()->randomElement(['partner-admin', 'partner-staff']),
            'token' => Str::random(40),
            'status' => fake()->randomElement(['pending', 'accepted', 'expired']),
            'expires_at' => CarbonImmutable::now()->addDays(7),
            'accepted_at' => null,
            'meta' => [
                'message' => fake()->randomElement([
                    'Welcome aboard from the Wild Atlantic team.',
                    'Invitation to join the Ireland partner crew.',
                    'Join us for coastal adventures across Ireland.',
                ]),
            ],
        ];
    }
}
