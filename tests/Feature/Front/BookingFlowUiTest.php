<?php

use App\Models\Booking;
use App\Models\Event;
use App\Models\Hold;
use App\Models\Location;
use App\Models\Partner;
use App\Models\Product;
use App\Models\SearchIndex;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a hold from the search page', function () {
    $partner = Partner::factory()->create(['status' => 'active']);
    $location = Location::factory()->for($partner)->create(['status' => 'active']);
    $product = Product::factory()->for($partner)->for($location)->create([
        'type' => 'event',
        'status' => 'active',
        'visibility' => 'public',
    ]);
    $event = Event::factory()->for($partner)->for($product)->create([
        'starts_at' => CarbonImmutable::today()->addDays(5)->setTime(9, 0),
        'ends_at' => CarbonImmutable::today()->addDays(5)->setTime(12, 0),
    ]);

    $searchIndex = SearchIndex::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
        'event_id' => $event->id,
        'unit_id' => null,
        'location_id' => $location->id,
        'starts_on' => $event->starts_at->toDateString(),
        'ends_on' => $event->ends_at->toDateString(),
        'capacity_available' => 5,
        'price_min' => 80,
        'price_max' => 120,
    ]);

    $component = app('livewire')->new('pages::front.search.index');
    $component->quantity = 2;
    $component->createHold($searchIndex->id, true);

    $hold = Hold::query()->first();

    expect($hold)->not->toBeNull()
        ->and($hold->product_id)->toBe($product->id)
        ->and($hold->event_id)->toBe($event->id);
});

it('creates a booking from a hold and confirms payment', function () {
    $partner = Partner::factory()->create(['status' => 'active']);
    $location = Location::factory()->for($partner)->create(['status' => 'active']);
    $product = Product::factory()->for($partner)->for($location)->create([
        'type' => 'event',
        'status' => 'active',
        'visibility' => 'public',
    ]);
    $event = Event::factory()->for($partner)->for($product)->create([
        'starts_at' => CarbonImmutable::today()->addDays(8)->setTime(9, 0),
        'ends_at' => CarbonImmutable::today()->addDays(8)->setTime(12, 0),
    ]);

    SearchIndex::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
        'event_id' => $event->id,
        'unit_id' => null,
        'location_id' => $location->id,
        'starts_on' => $event->starts_at->toDateString(),
        'ends_on' => $event->ends_at->toDateString(),
        'capacity_available' => 8,
        'price_min' => 95,
        'price_max' => 120,
    ]);

    $hold = Hold::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
        'event_id' => $event->id,
        'unit_id' => null,
        'starts_on' => $event->starts_at->toDateString(),
        'ends_on' => $event->ends_at->toDateString(),
        'quantity' => 2,
        'status' => 'active',
        'expires_at' => CarbonImmutable::now()->addMinutes(20),
    ]);

    $detailsComponent = app('livewire')->new('pages::front.booking.details');
    $detailsComponent->mount($hold);
    $detailsComponent->customerName = 'Jane Doe';
    $detailsComponent->customerEmail = 'jane@example.test';
    $detailsComponent->acceptTerms = true;
    $detailsComponent->createBooking();

    $booking = Booking::query()->first();

    expect($booking)->not->toBeNull()
        ->and($booking->status)->toBe('draft')
        ->and($booking->items)->toHaveCount(1);

    $confirmComponent = app('livewire')->new('pages::front.booking.confirm');
    $confirmComponent->mount($booking);
    $confirmComponent->paymentMethod = 'manual';
    $confirmComponent->status = 'captured';
    $confirmComponent->confirmBooking();

    expect($confirmComponent->confirmed)->toBeTrue();

    $booking->refresh();

    expect($booking->status)->toBe('confirmed')
        ->and($booking->payment_status)->toBe('paid');
});
