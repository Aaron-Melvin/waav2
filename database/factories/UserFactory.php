<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstNames = ['Liam', 'Noah', 'Conor', 'Sean', 'Ciaran', 'Eoin', 'Aoife', 'Saoirse', 'Niamh', 'Orla', 'Maeve', 'Siobhan'];
        $lastNames = ["O'Brien", "O'Connor", "Murphy", "Kelly", "Walsh", "Byrne", "O'Sullivan", "Ryan", "O'Neill", "O'Reilly", "Doyle", "Gallagher"];
        $first = fake()->randomElement($firstNames);
        $last = fake()->randomElement($lastNames);
        $name = $first.' '.$last;
        $email = Str::slug($first.'.'.$last).fake()->numberBetween(1, 9999).'@waa.ie';

        return [
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model has two-factor authentication configured.
     */
    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => encrypt('secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1'])),
            'two_factor_confirmed_at' => now(),
        ]);
    }
}
