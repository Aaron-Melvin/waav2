<?php

use App\Models\ApiClient;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Hold;
use App\Models\Invoice;
use App\Models\Partner;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Refund;
use App\Models\SearchIndex;
use App\Models\Unit;
use App\Models\UnitHoldLock;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function frontHeaders(ApiClient $client, string $secret): array
{
    return [
        'X-Client-Id' => $client->client_id,
        'X-Client-Secret' => $secret,
    ];
}

it('searches availability using the search index', function () {
    $partner = Partner::factory()->create();
    $product = Product::factory()->for($partner)->create();

    $availability = SearchIndex::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
        'capacity_available' => 5,
        'starts_on' => CarbonImmutable::today()->addDays(1),
        'ends_on' => CarbonImmutable::today()->addDays(1),
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('front-secret-availability')
        ->create(['client_id' => 'front_availability']);

    $response = $this->postJson('/api/v1/front/availability/search', [
        'product_id' => $product->id,
        'date_range' => [
            'from' => CarbonImmutable::today()->toDateString(),
            'to' => CarbonImmutable::today()->addDays(5)->toDateString(),
        ],
        'quantity' => 2,
    ], frontHeaders($apiClient, 'front-secret-availability'))
        ->assertSuccessful();

    $response->assertJsonPath('data.0.id', $availability->id);
});

it('creates holds for events and units', function () {
    $partner = Partner::factory()->create();
    $eventProduct = Product::factory()->for($partner)->create();
    $event = Event::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $eventProduct->id,
    ]);

    $unitProduct = Product::factory()->for($partner)->accommodation()->create();
    $unit = Unit::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $unitProduct->id,
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('front-secret-holds')
        ->create(['client_id' => 'front_holds']);

    $this->postJson('/api/v1/front/holds', [
        'product_id' => $eventProduct->id,
        'event_id' => $event->id,
        'quantity' => 2,
        'expires_in_minutes' => 20,
    ], frontHeaders($apiClient, 'front-secret-holds'))
        ->assertCreated()
        ->assertJsonPath('data.event_id', $event->id);

    $start = CarbonImmutable::today()->addDays(2);
    $end = $start->addDays(2);

    $this->postJson('/api/v1/front/holds', [
        'product_id' => $unitProduct->id,
        'unit_id' => $unit->id,
        'starts_on' => $start->toDateString(),
        'ends_on' => $end->toDateString(),
        'quantity' => 1,
    ], frontHeaders($apiClient, 'front-secret-holds'))
        ->assertCreated()
        ->assertJsonPath('data.unit_id', $unit->id);

    expect(Hold::count())->toBe(2);
    expect(UnitHoldLock::count())->toBeGreaterThan(0);
});

it('creates and confirms a booking from the front api', function () {
    $partner = Partner::factory()->create();
    $product = Product::factory()->for($partner)->create();
    $event = Event::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('front-secret-booking')
        ->create(['client_id' => 'front_booking']);

    $bookingResponse = $this->postJson('/api/v1/front/bookings', [
        'customer' => [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone_e164' => '+353861234567',
        ],
        'items' => [
            [
                'item_type' => 'event',
                'product_id' => $product->id,
                'event_id' => $event->id,
                'quantity' => 2,
            ],
        ],
        'terms_version' => '2026-01',
    ], frontHeaders($apiClient, 'front-secret-booking'))
        ->assertCreated();

    $bookingId = $bookingResponse->json('data.id');

    $this->postJson("/api/v1/front/bookings/{$bookingId}/confirm", [
        'payment_method' => 'manual',
    ], frontHeaders($apiClient, 'front-secret-booking'))
        ->assertSuccessful()
        ->assertJsonPath('data.status', 'confirmed');

    expect(Payment::count())->toBe(1);
    expect(Invoice::count())->toBe(1);
});

it('cancels a booking and creates a refund when requested', function () {
    $partner = Partner::factory()->create();
    $booking = Booking::factory()->create([
        'partner_id' => $partner->id,
        'status' => 'confirmed',
        'payment_status' => 'paid',
        'total_gross' => 120,
    ]);

    $payment = Payment::factory()->create([
        'partner_id' => $partner->id,
        'booking_id' => $booking->id,
        'amount' => 120,
        'status' => 'captured',
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('front-secret-cancel')
        ->create(['client_id' => 'front_cancel']);

    $this->postJson("/api/v1/front/bookings/{$booking->id}/cancel", [
        'reason' => 'Customer request',
        'refund' => true,
    ], frontHeaders($apiClient, 'front-secret-cancel'))
        ->assertSuccessful()
        ->assertJsonPath('data.status', 'cancelled');

    expect(Refund::count())->toBe(1);
    expect(Payment::query()->whereKey($payment->id)->value('status'))->toBe('refunded');
});
