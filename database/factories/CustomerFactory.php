<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
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
        $phone = '+353'.fake()->numberBetween(100000000, 999999999);

        return [
            'partner_id' => Partner::factory(),
            'name' => $name,
            'email' => $email,
            'phone_e164' => fake()->boolean(80) ? $phone : null,
            'marketing_opt_in' => fake()->boolean(35),
            'notifications_opt_in' => fake()->boolean(85),
            'password' => Hash::make('password'),
            'remember_token' => fake()->boolean(60) ? Str::random(10) : null,
        ];
    }
}
