<?php

use App\Models\ApiClient;
use App\Models\Event;
use App\Models\EventBlackout;
use App\Models\Location;
use App\Models\Partner;
use App\Models\Product;
use App\Models\RatePlan;
use App\Models\StaffInvitation;
use App\Models\Unit;
use App\Models\UnitCalendar;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

if (! function_exists('partnerHeaders')) {
    function partnerHeaders(ApiClient $client, string $secret): array
    {
        return [
            'X-Client-Id' => $client->client_id,
            'X-Client-Secret' => $secret,
        ];
    }
}

it('allows partners to manage locations', function () {
    $partner = Partner::factory()->create();
    $location = Location::factory()->for($partner)->create();

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-locations')
        ->create(['client_id' => 'partner_locations']);

    $headers = partnerHeaders($apiClient, 'partner-secret-locations');

    $this->getJson('/api/v1/partner/locations', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $location->id);

    $this->postJson('/api/v1/partner/locations', [
        'name' => 'Cliffs of Moher',
        'timezone' => 'Europe/Dublin',
        'status' => 'active',
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.name', 'Cliffs of Moher');

    $this->patchJson("/api/v1/partner/locations/{$location->id}", [
        'name' => 'Updated Location',
        'timezone' => $location->timezone,
    ], $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Updated Location');
});

it('allows partners to manage events, overrides, and blackouts', function () {
    $partner = Partner::factory()->create();
    $product = Product::factory()->for($partner)->create(['type' => 'event']);
    $event = Event::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
    ]);
    $blackout = EventBlackout::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-events')
        ->create(['client_id' => 'partner_events']);

    $headers = partnerHeaders($apiClient, 'partner-secret-events');

    $this->getJson('/api/v1/partner/events', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $event->id);

    $this->postJson('/api/v1/partner/events', [
        'product_id' => $product->id,
        'starts_at' => now()->addDay()->toDateTimeString(),
        'ends_at' => now()->addDays(2)->toDateTimeString(),
        'status' => 'scheduled',
        'publish_state' => 'draft',
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.product_id', $product->id);

    $this->patchJson("/api/v1/partner/events/{$event->id}", [
        'status' => 'completed',
        'publish_state' => 'published',
    ], $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.status', 'completed');

    $this->postJson("/api/v1/partner/events/{$event->id}/overrides", [
        'field' => 'notes',
        'value' => 'Bring rain gear',
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.field', 'notes');

    $this->getJson('/api/v1/partner/blackouts', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $blackout->id);

    $this->postJson('/api/v1/partner/blackouts', [
        'product_id' => $product->id,
        'starts_at' => now()->addWeek()->toDateString(),
        'ends_at' => now()->addWeeks(2)->toDateString(),
        'status' => 'active',
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.product_id', $product->id);

    $this->patchJson("/api/v1/partner/blackouts/{$blackout->id}", [
        'product_id' => $product->id,
        'starts_at' => $blackout->starts_at->toDateString(),
        'ends_at' => $blackout->ends_at->toDateString(),
        'status' => 'inactive',
    ], $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.status', 'inactive');
});

it('allows partners to manage units and calendars', function () {
    $partner = Partner::factory()->create();
    $product = Product::factory()->for($partner)->create(['type' => 'accommodation']);
    $unit = Unit::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
    ]);
    UnitCalendar::factory()->create([
        'partner_id' => $partner->id,
        'unit_id' => $unit->id,
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-units')
        ->create(['client_id' => 'partner_units']);

    $headers = partnerHeaders($apiClient, 'partner-secret-units');

    $this->getJson("/api/v1/partner/products/{$product->id}/units", $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $unit->id);

    $this->postJson("/api/v1/partner/products/{$product->id}/units", [
        'name' => 'Room 101',
        'code' => 'RM-101',
        'occupancy_adults' => 2,
        'occupancy_children' => 1,
        'status' => 'active',
        'housekeeping_required' => true,
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.code', 'RM-101');

    $this->postJson("/api/v1/partner/products/{$product->id}/units/{$unit->id}/calendar", [
        'date' => now()->addDay()->toDateString(),
        'is_available' => true,
        'min_stay_nights' => 2,
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.unit_id', $unit->id);
});

it('allows partners to manage rate plans and prices', function () {
    $partner = Partner::factory()->create();
    $product = Product::factory()->for($partner)->create(['type' => 'accommodation']);
    $ratePlan = RatePlan::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-rates')
        ->create(['client_id' => 'partner_rates']);

    $headers = partnerHeaders($apiClient, 'partner-secret-rates');

    $this->getJson("/api/v1/partner/products/{$product->id}/rate-plans", $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $ratePlan->id);

    $this->postJson("/api/v1/partner/products/{$product->id}/rate-plans", [
        'name' => 'Standard',
        'pricing_model' => 'per_night',
        'currency' => 'EUR',
        'status' => 'active',
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.name', 'Standard');

    $this->postJson("/api/v1/partner/products/{$product->id}/rate-plans/{$ratePlan->id}/prices", [
        'starts_on' => now()->toDateString(),
        'ends_on' => now()->addDays(7)->toDateString(),
        'price' => 120,
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.rate_plan_id', $ratePlan->id);
});

it('allows partners to manage staff invitations', function () {
    $partner = Partner::factory()->create();
    $invitation = StaffInvitation::factory()->create([
        'partner_id' => $partner->id,
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-staff')
        ->create(['client_id' => 'partner_staff']);

    $headers = partnerHeaders($apiClient, 'partner-secret-staff');

    $this->getJson('/api/v1/partner/staff-invitations', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $invitation->id);

    $this->postJson('/api/v1/partner/staff-invitations', [
        'email' => 'staff@example.test',
        'role' => 'partner-staff',
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.email', 'staff@example.test');
});

it('enforces idempotency keys for partner mutations', function () {
    $partner = Partner::factory()->create();

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-idempotency')
        ->create(['client_id' => 'partner_idempotency']);

    $headers = array_merge(partnerHeaders($apiClient, 'partner-secret-idempotency'), [
        'Idempotency-Key' => 'location-key-1',
    ]);

    $payload = [
        'name' => 'Loop Head',
        'timezone' => 'Europe/Dublin',
    ];

    $first = $this->postJson('/api/v1/partner/locations', $payload, $headers)
        ->assertCreated()
        ->json('data.id');

    $second = $this->postJson('/api/v1/partner/locations', $payload, $headers)
        ->assertSuccessful()
        ->json('data.id');

    expect($first)->toBe($second);
    expect(Location::query()->where('partner_id', $partner->id)->count())->toBe(1);

    $this->postJson('/api/v1/partner/locations', [
        'name' => 'Different',
        'timezone' => 'Europe/Dublin',
    ], $headers)->assertStatus(409);
});
