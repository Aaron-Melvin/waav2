<?php

use App\Models\Customer;
use App\Models\Location;
use App\Models\Partner;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('builds Ireland-focused seed data', function () {
    $partner = Partner::factory()->create();

    expect($partner->currency)->toBe('EUR');
    expect($partner->timezone)->toBe('Europe/Dublin');
    expect(str_ends_with($partner->billing_email, '.ie'))->toBeTrue();

    $location = Location::factory()->for($partner)->create();
    $counties = ['Galway', 'Cork', 'Dublin', 'Limerick', 'Kilkenny', 'Kerry', 'Mayo', 'Sligo', 'Donegal', 'Clare', 'Wexford', 'Waterford', 'Wicklow'];

    expect($location->country_code)->toBe('IE');
    expect($location->timezone)->toBe('Europe/Dublin');
    expect(in_array($location->region, $counties, true))->toBeTrue();

    $product = Product::factory()->for($partner)->create(['type' => 'event']);
    $activityKeywords = ['Surf', 'Paddle', 'Kayak', 'Hike', 'Cliff', 'Atlantic'];

    expect(Str::contains($product->name, $activityKeywords))->toBeTrue();

    $customer = Customer::factory()->for($partner)->create();
    $user = User::factory()->for($partner)->create();

    expect(str_ends_with($customer->email, '.ie'))->toBeTrue();
    expect(str_ends_with($user->email, '.ie'))->toBeTrue();
});
