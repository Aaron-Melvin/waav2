<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $activities = [
            'Surf Session',
            'Stand Up Paddle Boarding',
            'Sea Kayaking',
            'Kayaking Adventure',
            'Coastal Hike',
            'Mountain Hike',
            'Cliff Walk',
            'Atlantic Trail Hike',
            'Harbour Paddle',
        ];
        $places = [
            ['name' => 'Bundoran', 'county' => 'Donegal'],
            ['name' => 'Lahinch', 'county' => 'Clare'],
            ['name' => 'Doolin', 'county' => 'Clare'],
            ['name' => 'Dingle', 'county' => 'Kerry'],
            ['name' => 'Killarney', 'county' => 'Kerry'],
            ['name' => 'Westport', 'county' => 'Mayo'],
            ['name' => 'Achill Island', 'county' => 'Mayo'],
            ['name' => 'Sligo', 'county' => 'Sligo'],
            ['name' => 'Galway', 'county' => 'Galway'],
            ['name' => 'Howth', 'county' => 'Dublin'],
            ['name' => 'Wicklow', 'county' => 'Wicklow'],
            ['name' => 'Kilkee', 'county' => 'Clare'],
        ];
        $place = fake()->randomElement($places);
        $activity = fake()->randomElement($activities);
        $suffix = fake()->unique()->numberBetween(10, 9999);
        $name = "{$place['name']} {$activity} {$suffix}";
        $partner = Partner::factory();
        $meetingPoints = [
            'Harbour Road',
            'Quay Street',
            'Pier Road',
            'Coast Road',
            'Cliff Road',
            'Seafront Walk',
        ];
        $meetingPoint = fake()->randomElement($meetingPoints).', '.$place['name'].', Co. '.$place['county'];
        $descriptions = [
            'Explore the Wild Atlantic Way with a local guide and all equipment included.',
            'Experience Ireland’s coastline with a small-group adventure and expert instruction.',
            'A relaxed Irish outdoor session with stunning Atlantic views and friendly guides.',
            'Discover Ireland’s coastal trails and hidden bays on a guided outdoor experience.',
        ];

        return [
            'partner_id' => $partner,
            'location_id' => Location::factory()->for($partner),
            'name' => $name,
            'type' => 'event',
            'slug' => Str::slug($name),
            'description' => fake()->randomElement($descriptions),
            'capacity_total' => fake()->numberBetween(6, 40),
            'default_currency' => 'EUR',
            'status' => 'active',
            'visibility' => 'public',
            'lead_time_minutes' => fake()->randomElement([60, 120, 240, 480]),
            'cutoff_minutes' => fake()->randomElement([30, 60, 120]),
            'meta' => [
                'duration_minutes' => fake()->randomElement([60, 90, 120, 180]),
                'meeting_point' => $meetingPoint,
                'included' => ['guide', 'equipment', 'safety briefing'],
            ],
        ];
    }

    public function accommodation(): static
    {
        return $this->state(function () {
            $places = [
                'Dingle',
                'Killarney',
                'Westport',
                'Galway',
                'Clifden',
                'Sligo',
                'Wicklow',
                'Cork',
                'Kerry',
                'Donegal',
            ];
            $lodgings = [
                'Coastal Cottage',
                'Harbour Lodge',
                'Atlantic Retreat',
                'Lakeside Inn',
                'Hilltop Guesthouse',
                'Seaside B&B',
            ];
            $name = fake()->randomElement($places).' '.fake()->randomElement($lodgings).' '.fake()->unique()->numberBetween(10, 9999);

            return [
                'name' => $name,
                'type' => 'accommodation',
                'slug' => Str::slug($name),
                'capacity_total' => fake()->numberBetween(10, 80),
                'meta' => [
                    'check_in_time' => '15:00',
                    'check_out_time' => '11:00',
                    'amenities' => ['wifi', 'parking', 'breakfast', 'ocean_view'],
                    'house_rules' => ['no smoking', 'quiet hours after 22:00', 'respect the countryside'],
                ],
            ];
        });
    }
}
