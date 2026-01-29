<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $places = [
            ['city' => 'Galway', 'county' => 'Galway', 'lat' => 53.2707, 'lng' => -9.0568],
            ['city' => 'Cork', 'county' => 'Cork', 'lat' => 51.8985, 'lng' => -8.4756],
            ['city' => 'Dublin', 'county' => 'Dublin', 'lat' => 53.3498, 'lng' => -6.2603],
            ['city' => 'Limerick', 'county' => 'Limerick', 'lat' => 52.6638, 'lng' => -8.6267],
            ['city' => 'Kilkenny', 'county' => 'Kilkenny', 'lat' => 52.6541, 'lng' => -7.2448],
            ['city' => 'Killarney', 'county' => 'Kerry', 'lat' => 52.0599, 'lng' => -9.5044],
            ['city' => 'Dingle', 'county' => 'Kerry', 'lat' => 52.1408, 'lng' => -10.2686],
            ['city' => 'Westport', 'county' => 'Mayo', 'lat' => 53.7998, 'lng' => -9.5233],
            ['city' => 'Sligo', 'county' => 'Sligo', 'lat' => 54.2766, 'lng' => -8.4761],
            ['city' => 'Bundoran', 'county' => 'Donegal', 'lat' => 54.4778, 'lng' => -8.2809],
            ['city' => 'Lahinch', 'county' => 'Clare', 'lat' => 52.9336, 'lng' => -9.3491],
            ['city' => 'Doolin', 'county' => 'Clare', 'lat' => 53.0094, 'lng' => -9.3776],
            ['city' => 'Ennis', 'county' => 'Clare', 'lat' => 52.8436, 'lng' => -8.9864],
            ['city' => 'Tralee', 'county' => 'Kerry', 'lat' => 52.2700, 'lng' => -9.7029],
            ['city' => 'Wexford', 'county' => 'Wexford', 'lat' => 52.3369, 'lng' => -6.4627],
            ['city' => 'Waterford', 'county' => 'Waterford', 'lat' => 52.2593, 'lng' => -7.1101],
            ['city' => 'Bray', 'county' => 'Wicklow', 'lat' => 53.2020, 'lng' => -6.0983],
            ['city' => 'Greystones', 'county' => 'Wicklow', 'lat' => 53.1408, 'lng' => -6.0631],
            ['city' => 'Howth', 'county' => 'Dublin', 'lat' => 53.3877, 'lng' => -6.0653],
            ['city' => 'Malahide', 'county' => 'Dublin', 'lat' => 53.4509, 'lng' => -6.1544],
            ['city' => 'Letterkenny', 'county' => 'Donegal', 'lat' => 54.9496, 'lng' => -7.7337],
            ['city' => 'Clifden', 'county' => 'Galway', 'lat' => 53.4881, 'lng' => -10.0186],
            ['city' => 'Achill Sound', 'county' => 'Mayo', 'lat' => 53.9562, 'lng' => -10.0042],
        ];

        $streets = [
            'Main Street',
            'Harbour Road',
            'Quay Street',
            'Market Square',
            'Coast Road',
            'Pier Road',
            'Sea Road',
            'High Street',
            'Cliff Road',
            "St Patrick's Road",
        ];

        $routingKeys = ['D01', 'D02', 'D04', 'D06', 'H91', 'V92', 'V94', 'P85', 'F92', 'A94', 'T12', 'T23', 'X91', 'Y35'];
        $place = fake()->randomElement($places);
        $street = fake()->randomElement($streets);
        $addressLine2 = fake()->boolean(35) ? fake()->randomElement(['Harbour View', 'Pier View', 'Coastal Park', 'Quayside', 'Market Lane']) : null;
        $eircode = fake()->randomElement($routingKeys).' '.strtoupper(fake()->bothify('??##'));

        return [
            'partner_id' => Partner::factory(),
            'name' => $place['city'].' Adventure Base',
            'address_line_1' => fake()->numberBetween(1, 220).' '.$street,
            'address_line_2' => $addressLine2,
            'city' => $place['city'],
            'region' => $place['county'],
            'postal_code' => $eircode,
            'country_code' => 'IE',
            'latitude' => $place['lat'],
            'longitude' => $place['lng'],
            'timezone' => 'Europe/Dublin',
            'status' => 'active',
        ];
    }
}
