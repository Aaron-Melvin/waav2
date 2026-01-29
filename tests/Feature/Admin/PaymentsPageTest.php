<?php

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows super admins to access the payments overview page', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $this->actingAs($user)
        ->get('/admin/payments')
        ->assertSuccessful()
        ->assertSee('Payments overview');
});

it('blocks non-admin users from the payments overview page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/payments')
        ->assertForbidden();
});

it('allows super admins to access payment detail pages', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $payment = Payment::factory()->create();

    $this->actingAs($user)
        ->get("/admin/payments/{$payment->id}")
        ->assertSuccessful()
        ->assertSee('Payment details');
});

it('blocks non-admin users from payment detail pages', function () {
    $user = User::factory()->create();
    $payment = Payment::factory()->create();

    $this->actingAs($user)
        ->get("/admin/payments/{$payment->id}")
        ->assertForbidden();
});
