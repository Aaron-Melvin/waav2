<?php

use App\Models\Partner;
use App\Models\Product;
use App\Models\SearchIndex;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the customer search page', function () {
    $this->get('/front')
        ->assertOk()
        ->assertSee('Search availability');
});

it('returns results without a selected product when date range is set', function () {
    $partner = Partner::factory()->create(['status' => 'active']);
    $product = Product::factory()->for($partner)->create([
        'status' => 'active',
        'visibility' => 'public',
    ]);
    $start = CarbonImmutable::today()->addDays(2)->toDateString();

    SearchIndex::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
        'starts_on' => $start,
        'ends_on' => $start,
        'capacity_available' => 5,
    ]);

    $component = app('livewire')->new('pages::front.search.index');
    $component->dateRange = ['start' => $start, 'end' => $start];
    $component->hasSearched = true;

    $results = $component->getResultsProperty();

    expect($results)->toHaveCount(1);
});

it('filters results by product type', function () {
    $partner = Partner::factory()->create(['status' => 'active']);
    $eventProduct = Product::factory()->for($partner)->create([
        'type' => 'event',
        'status' => 'active',
        'visibility' => 'public',
    ]);
    $accommodationProduct = Product::factory()->for($partner)->create([
        'type' => 'accommodation',
        'status' => 'active',
        'visibility' => 'public',
    ]);
    $start = CarbonImmutable::today()->addDays(3)->toDateString();

    SearchIndex::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $eventProduct->id,
        'starts_on' => $start,
        'ends_on' => $start,
        'capacity_available' => 4,
    ]);

    SearchIndex::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $accommodationProduct->id,
        'starts_on' => $start,
        'ends_on' => $start,
        'capacity_available' => 4,
    ]);

    $component = app('livewire')->new('pages::front.search.index');
    $component->dateRange = ['start' => $start, 'end' => $start];
    $component->productType = 'event';
    $component->hasSearched = true;

    $results = $component->getResultsProperty();

    expect($results)->toHaveCount(1)
        ->and($results->first()->product_id)->toBe($eventProduct->id);
});

it('keeps results visible when changing sort after searching', function () {
    $partner = Partner::factory()->create(['status' => 'active']);
    $product = Product::factory()->for($partner)->create([
        'status' => 'active',
        'visibility' => 'public',
    ]);
    $start = CarbonImmutable::today()->addDays(4)->toDateString();

    SearchIndex::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
        'starts_on' => $start,
        'ends_on' => $start,
        'capacity_available' => 3,
    ]);

    $component = app('livewire')->new('pages::front.search.index');
    $component->dateRange = ['start' => $start, 'end' => $start];
    $component->hasSearched = true;
    $component->sort = 'price_low';
    $component->updatedSort();

    $results = $component->getResultsProperty();

    expect($results)->toHaveCount(1)
        ->and($component->hasSearched)->toBeTrue();
});
