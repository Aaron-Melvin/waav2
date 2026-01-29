<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class PartnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = [
            'Wild Atlantic Adventures',
            'Connemara Coast Co',
            'Burren Hiking Co',
            'Sligo Surf School',
            'Dingle Bay Outdoors',
            'Achill Island Adventures',
            'Galway Sea Sessions',
            'Wicklow Hill Walks',
            'Cork Kayak Collective',
            'Clare Paddle Co',
            'Mayo Mountain Trails',
            'Kerry Lakeside Retreats',
            'Donegal Ocean Tours',
            'Lough Derg Getaways',
            'Westport Outdoor Club',
        ];

        $baseName = fake()->randomElement($names);
        $suffix = fake()->unique()->numberBetween(100, 9999);
        $name = "{$baseName} {$suffix}";
        $slug = Str::slug($name);

        return [
            'name' => $name,
            'slug' => $slug,
            'billing_email' => "hello@{$slug}.ie",
            'currency' => 'EUR',
            'timezone' => 'Europe/Dublin',
            'status' => 'active',
        ];
    }
}
