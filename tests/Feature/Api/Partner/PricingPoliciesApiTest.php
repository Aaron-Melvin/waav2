<?php

use App\Models\ApiClient;
use App\Models\CancellationPolicy;
use App\Models\EventSeries;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Tax;
use App\Models\Fee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\CarbonImmutable;

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

it('allows partners to manage event series', function () {
    $partner = Partner::factory()->create();
    $product = Product::factory()->for($partner)->create(['type' => 'event']);
    $series = EventSeries::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-series')
        ->create(['client_id' => 'partner_series']);

    $headers = partnerHeaders($apiClient, 'partner-secret-series');

    $this->getJson('/api/v1/partner/event-series', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $series->id);

    $this->postJson('/api/v1/partner/event-series', [
        'product_id' => $product->id,
        'name' => 'Morning Tour',
        'starts_at' => '09:00',
        'ends_at' => '11:00',
        'timezone' => 'Europe/Dublin',
        'status' => 'active',
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.name', 'Morning Tour');

    $this->patchJson("/api/v1/partner/event-series/{$series->id}", [
        'product_id' => $product->id,
        'name' => 'Updated Series',
        'starts_at' => '10:00',
        'ends_at' => '12:00',
        'timezone' => 'Europe/Dublin',
        'status' => 'inactive',
    ], $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Updated Series');
});

it('allows partners to manage taxes, fees, and cancellation policies', function () {
    $partner = Partner::factory()->create();
    $tax = Tax::factory()->create(['partner_id' => $partner->id]);
    $fee = Fee::factory()->create(['partner_id' => $partner->id]);
    $policy = CancellationPolicy::factory()->create(['partner_id' => $partner->id]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-pricing')
        ->create(['client_id' => 'partner_pricing']);

    $headers = partnerHeaders($apiClient, 'partner-secret-pricing');

    $this->getJson('/api/v1/partner/taxes', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $tax->id);

    $this->postJson('/api/v1/partner/taxes', [
        'name' => 'VAT',
        'rate' => 0.2,
        'applies_to' => 'booking',
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.name', 'VAT');

    $this->getJson('/api/v1/partner/fees', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $fee->id);

    $this->postJson('/api/v1/partner/fees', [
        'name' => 'Service Fee',
        'type' => 'flat',
        'amount' => 5,
        'applies_to' => 'booking',
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.name', 'Service Fee');

    $this->getJson('/api/v1/partner/cancellation-policies', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $policy->id);

    $this->postJson('/api/v1/partner/cancellation-policies', [
        'name' => 'Flexible',
        'rules' => [
            ['window_hours' => 24, 'fee_percent' => 10],
        ],
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.name', 'Flexible');
});

it('generates events from event series rules', function () {
    $partner = Partner::factory()->create();
    $product = Product::factory()->for($partner)->create(['type' => 'event']);
    $series = EventSeries::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
        'starts_at' => '09:00:00',
        'ends_at' => '11:00:00',
        'recurrence_rule' => [
            'frequency' => 'weekly',
            'interval' => 1,
            'byweekday' => ['MO', 'WE'],
        ],
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-generate')
        ->create(['client_id' => 'partner_generate']);

    $headers = partnerHeaders($apiClient, 'partner-secret-generate');
    $from = CarbonImmutable::parse('2026-02-02');
    $to = CarbonImmutable::parse('2026-02-12');

    $response = $this->postJson("/api/v1/partner/event-series/{$series->id}/generate", [
        'date_range' => [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ],
    ], $headers)->assertSuccessful();

    $response->assertJsonPath('created', 4);
});
