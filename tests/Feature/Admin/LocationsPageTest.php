<?php

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows super admins to access the locations overview page', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $this->actingAs($user)
        ->get('/admin/locations')
        ->assertSuccessful()
        ->assertSee('Locations');
});

it('blocks non-admin users from the locations overview page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/locations')
        ->assertForbidden();
});

it('allows super admins to access location detail pages', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $location = Location::factory()->create();

    $this->actingAs($user)
        ->get("/admin/locations/{$location->id}")
        ->assertSuccessful()
        ->assertSee($location->name);
});

it('blocks non-admin users from location detail pages', function () {
    $user = User::factory()->create();
    $location = Location::factory()->create();

    $this->actingAs($user)
        ->get("/admin/locations/{$location->id}")
        ->assertForbidden();
});
