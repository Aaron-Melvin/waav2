<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlatformUser>
 */
class PlatformUserFactory extends Factory
{
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
            'role' => fake()->randomElement(['super-admin', 'support']),
            'status' => 'active',
            'password' => 'password',
            'remember_token' => Str::random(10),
        ];
    }
}
