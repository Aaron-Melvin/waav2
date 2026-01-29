<?php

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows super admins to access the bookings overview page', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $this->actingAs($user)
        ->get('/admin/bookings')
        ->assertSuccessful()
        ->assertSee('Bookings overview');
});

it('blocks non-admin users from the bookings overview page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/bookings')
        ->assertForbidden();
});

it('allows super admins to access booking detail pages', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $booking = Booking::factory()->create();

    $this->actingAs($user)
        ->get("/admin/bookings/{$booking->id}")
        ->assertSuccessful()
        ->assertSee('Booking');
});

it('blocks non-admin users from booking detail pages', function () {
    $user = User::factory()->create();
    $booking = Booking::factory()->create();

    $this->actingAs($user)
        ->get("/admin/bookings/{$booking->id}")
        ->assertForbidden();
});
