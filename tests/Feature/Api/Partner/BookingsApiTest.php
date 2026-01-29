<?php

use App\Models\ApiClient;
use App\Models\Booking;
use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists partner bookings with filters', function () {
    $partner = Partner::factory()->create();
    $otherPartner = Partner::factory()->create();

    $booking = Booking::factory()->create([
        'partner_id' => $partner->id,
        'status' => 'confirmed',
    ]);

    Booking::factory()->create([
        'partner_id' => $otherPartner->id,
        'status' => 'confirmed',
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-bookings')
        ->create(['client_id' => 'partner_bookings']);

    $this->getJson('/api/v1/partner/bookings?status=confirmed', [
        'X-Client-Id' => $apiClient->client_id,
        'X-Client-Secret' => 'partner-secret-bookings',
    ])
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $booking->id);
});

it('allows partners to update booking status', function () {
    $partner = Partner::factory()->create();
    $booking = Booking::factory()->create([
        'partner_id' => $partner->id,
        'status' => 'confirmed',
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-update')
        ->create(['client_id' => 'partner_update']);

    $this->patchJson("/api/v1/partner/bookings/{$booking->id}", [
        'status' => 'completed',
        'note' => 'Service delivered',
    ], [
        'X-Client-Id' => $apiClient->client_id,
        'X-Client-Secret' => 'partner-secret-update',
    ])
        ->assertSuccessful()
        ->assertJsonPath('data.status', 'completed');
});
