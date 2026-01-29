<?php

use App\Models\Booking;
use App\Models\Partner;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows super admins to create refunds for payments', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $partner = Partner::factory()->create();
    $booking = Booking::factory()->for($partner)->create();
    $payment = Payment::factory()->create([
        'partner_id' => $partner->id,
        'booking_id' => $booking->id,
        'amount' => 150,
        'currency' => 'EUR',
    ]);

    $this->actingAs($user)
        ->postJson("/api/v1/admin/payments/{$payment->id}/refunds", [
            'amount' => 50,
            'reason' => 'Customer request',
        ])
        ->assertCreated()
        ->assertJsonPath('data.payment_id', $payment->id)
        ->assertJsonPath('data.status', 'pending');
});

it('blocks non-admins from refunding payments', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();

    $partner = Partner::factory()->create();
    $booking = Booking::factory()->for($partner)->create();
    $payment = Payment::factory()->create([
        'partner_id' => $partner->id,
        'booking_id' => $booking->id,
        'amount' => 120,
    ]);

    $this->actingAs($user)
        ->postJson("/api/v1/admin/payments/{$payment->id}/refunds", [
            'amount' => 50,
        ])
        ->assertForbidden();
});
