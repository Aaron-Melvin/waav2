<?php

use App\Models\ApiClient;
use App\Models\Location;
use App\Models\Partner;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists partner products with filters', function () {
    $partner = Partner::factory()->create();
    $otherPartner = Partner::factory()->create();

    $product = Product::factory()->for($partner)->create([
        'status' => 'active',
        'type' => 'event',
    ]);

    Product::factory()->for($otherPartner)->create([
        'status' => 'active',
        'type' => 'event',
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-products')
        ->create(['client_id' => 'partner_products']);

    $this->getJson('/api/v1/partner/products?status=active&type=event', [
        'X-Client-Id' => $apiClient->client_id,
        'X-Client-Secret' => 'partner-secret-products',
    ])
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $product->id);
});

it('creates a partner product', function () {
    $partner = Partner::factory()->create(['currency' => 'EUR']);
    $location = Location::factory()->for($partner)->create();

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-create')
        ->create(['client_id' => 'partner_products_create']);

    $this->postJson('/api/v1/partner/products', [
        'name' => 'Coastal Walk',
        'type' => 'event',
        'location_id' => $location->id,
        'capacity_total' => 12,
        'lead_time_minutes' => 60,
        'cutoff_minutes' => 30,
    ], [
        'X-Client-Id' => $apiClient->client_id,
        'X-Client-Secret' => 'partner-secret-create',
    ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Coastal Walk')
        ->assertJsonPath('data.slug', 'coastal-walk')
        ->assertJsonPath('data.location_id', $location->id);

    $this->assertDatabaseHas('products', [
        'partner_id' => $partner->id,
        'name' => 'Coastal Walk',
        'location_id' => $location->id,
    ]);
});
