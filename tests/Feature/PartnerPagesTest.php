<?php

use App\Models\Event;
use App\Models\EventBlackout;
use App\Models\CancellationPolicy;
use App\Models\Fee;
use App\Models\Location;
use App\Models\Partner;
use App\Models\Product;
use App\Models\RatePlan;
use App\Models\RatePlanPrice;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\UnitCalendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows partner admins to access catalog and availability pages', function () {
    Role::create(['name' => 'partner-admin']);

    $partner = Partner::factory()->create();
    $user = User::factory()->create(['partner_id' => $partner->id]);
    $user->assignRole('partner-admin');

    $this->actingAs($user)
        ->get('/partner/catalog/products')
        ->assertSuccessful()
        ->assertSee('Catalog products');

    $this->actingAs($user)
        ->get('/partner/catalog/locations')
        ->assertSuccessful()
        ->assertSee('Catalog locations');

    $this->actingAs($user)
        ->get('/partner/availability/events')
        ->assertSuccessful()
        ->assertSee('Event availability');
});

it('allows partner staff to access catalog and availability pages', function () {
    Role::create(['name' => 'partner-staff']);

    $partner = Partner::factory()->create();
    $user = User::factory()->create(['partner_id' => $partner->id]);
    $user->assignRole('partner-staff');

    $this->actingAs($user)
        ->get('/partner/catalog/products')
        ->assertSuccessful();

    $this->actingAs($user)
        ->get('/partner/availability/events')
        ->assertSuccessful();
});

it('allows partner admins to view product, location, and event detail pages', function () {
    Role::create(['name' => 'partner-admin']);

    $partner = Partner::factory()->create();
    $user = User::factory()->create(['partner_id' => $partner->id]);
    $user->assignRole('partner-admin');

    $location = Location::factory()->for($partner)->create();
    $product = Product::factory()->for($partner)->create(['location_id' => $location->id]);
    $event = Event::factory()->for($partner)->for($product)->create();

    $this->actingAs($user)
        ->get("/partner/catalog/products/{$product->id}")
        ->assertSuccessful()
        ->assertSee($product->name);

    $this->actingAs($user)
        ->get("/partner/catalog/locations/{$location->id}")
        ->assertSuccessful()
        ->assertSee($location->name);

    $this->actingAs($user)
        ->get("/partner/availability/events/{$event->id}")
        ->assertSuccessful()
        ->assertSee('Availability settings');
});

it('allows partner admins to access pricing and inventory pages', function () {
    Role::create(['name' => 'partner-admin']);

    $partner = Partner::factory()->create();
    $user = User::factory()->create(['partner_id' => $partner->id]);
    $user->assignRole('partner-admin');

    $accommodation = Product::factory()->for($partner)->create(['type' => 'accommodation']);
    $ratePlan = RatePlan::factory()->for($partner)->for($accommodation)->create();
    $unit = Unit::factory()->for($partner)->for($accommodation)->create();

    $eventProduct = Product::factory()->for($partner)->create(['type' => 'event']);
    $event = Event::factory()->for($partner)->for($eventProduct)->create();
    $location = Location::factory()->for($partner)->create();
    $blackout = EventBlackout::factory()->for($partner)->create([
        'product_id' => $eventProduct->id,
        'location_id' => $location->id,
    ]);

    $this->actingAs($user)
        ->get("/partner/catalog/products/{$accommodation->id}/rate-plans")
        ->assertSuccessful()
        ->assertSee('Rate plans');

    $this->actingAs($user)
        ->get("/partner/catalog/products/{$accommodation->id}/rate-plans/{$ratePlan->id}")
        ->assertSuccessful()
        ->assertSee($ratePlan->name);

    $this->actingAs($user)
        ->get("/partner/catalog/products/{$accommodation->id}/units")
        ->assertSuccessful()
        ->assertSee('Units');

    $this->actingAs($user)
        ->get("/partner/catalog/products/{$accommodation->id}/units/{$unit->id}")
        ->assertSuccessful()
        ->assertSee($unit->name);

    $this->actingAs($user)
        ->get("/partner/availability/events/{$event->id}/overrides")
        ->assertSuccessful()
        ->assertSee('Event overrides');

    $this->actingAs($user)
        ->get('/partner/availability/blackouts')
        ->assertSuccessful()
        ->assertSee('Event blackouts');

    $this->actingAs($user)
        ->get("/partner/availability/blackouts/{$blackout->id}")
        ->assertSuccessful()
        ->assertSee('Blackout details');

    $this->actingAs($user)
        ->get('/partner/policies/cancellation')
        ->assertSuccessful()
        ->assertSee('Cancellation policies');

    $this->actingAs($user)
        ->get('/partner/policies/taxes')
        ->assertSuccessful()
        ->assertSee('Taxes');

    $this->actingAs($user)
        ->get('/partner/policies/fees')
        ->assertSuccessful()
        ->assertSee('Fees');
});

it('allows partner admins to adjust event publish state and capacity', function () {
    Role::create(['name' => 'partner-admin']);

    $partner = Partner::factory()->create();
    $user = User::factory()->create(['partner_id' => $partner->id]);
    $user->assignRole('partner-admin');

    $product = Product::factory()->for($partner)->create();
    $event = Event::factory()->for($partner)->for($product)->create([
        'publish_state' => 'draft',
        'capacity_total' => 12,
        'capacity_reserved' => 2,
    ]);

    $this->actingAs($user);

    $component = app('livewire')->new('pages::partner.availability.events.show');
    $component->mount($event);

    $component->publishState = 'published';
    $component->capacityTotal = '20';
    $component->capacityReserved = '5';
    $component->updateEvent();

    $event->refresh();

    expect($event->publish_state)->toBe('published');
    expect($event->capacity_total)->toBe(20);
    expect($event->capacity_reserved)->toBe(5);
});

it('allows partner admins to create products, locations, and events', function () {
    Role::create(['name' => 'partner-admin']);

    $partner = Partner::factory()->create();
    $user = User::factory()->create(['partner_id' => $partner->id]);
    $user->assignRole('partner-admin');

    $this->actingAs($user);

    $locationComponent = app('livewire')->new('pages::partner.catalog.locations.index');
    $locationComponent->mount();
    $locationComponent->createName = 'Harbour Basecamp';
    $locationComponent->createCity = 'Lahinch';
    $locationComponent->createRegion = 'Clare';
    $locationComponent->createCountryCode = 'IE';
    $locationComponent->createTimezone = 'Europe/Dublin';
    $locationComponent->createStatus = 'active';
    $locationComponent->createLocation();

    $location = Location::query()->firstWhere('name', 'Harbour Basecamp');

    expect($location)->not->toBeNull();

    $productComponent = app('livewire')->new('pages::partner.catalog.products.index');
    $productComponent->mount();
    $productComponent->createName = 'Cliffs Kayak Adventure';
    $productComponent->createType = 'event';
    $productComponent->createLocationId = $location->id;
    $productComponent->createStatus = 'active';
    $productComponent->createVisibility = 'public';
    $productComponent->createProduct();

    $product = Product::query()->firstWhere('name', 'Cliffs Kayak Adventure');

    expect($product)->not->toBeNull()
        ->and($product->partner_id)->toBe($partner->id);

    $eventComponent = app('livewire')->new('pages::partner.availability.events.index');
    $eventComponent->mount();
    $eventComponent->createProductId = $product->id;
    $eventComponent->createStartsAt = now()->addDay()->toDateTimeString();
    $eventComponent->createEndsAt = now()->addDays(2)->toDateTimeString();
    $eventComponent->createStatus = 'scheduled';
    $eventComponent->createPublishState = 'draft';
    $eventComponent->createEvent();

    expect(Event::query()->where('product_id', $product->id)->exists())->toBeTrue();
});

it('allows partner admins to create units and accommodation policies', function () {
    Role::create(['name' => 'partner-admin']);

    $partner = Partner::factory()->create();
    $user = User::factory()->create(['partner_id' => $partner->id]);
    $user->assignRole('partner-admin');

    $product = Product::factory()->for($partner)->create(['type' => 'accommodation']);

    $this->actingAs($user);

    $unitComponent = app('livewire')->new('pages::partner.catalog.products.units.index');
    $unitComponent->mount($product);
    $unitComponent->createName = 'Ocean Suite';
    $unitComponent->createOccupancyAdults = '2';
    $unitComponent->createOccupancyChildren = '1';
    $unitComponent->createStatus = 'active';
    $unitComponent->createHousekeepingRequired = '1';
    $unitComponent->createMetaJson = '{"amenities":["wifi","parking"]}';
    $unitComponent->createUnit();

    $unit = Unit::query()->where('product_id', $product->id)->first();

    expect($unit)->not->toBeNull()
        ->and($unit->meta)->toMatchArray(['amenities' => ['wifi', 'parking']]);

    $policyComponent = app('livewire')->new('pages::partner.policies.cancellation.index');
    $policyComponent->mount();
    $policyComponent->createName = 'Standard';
    $policyComponent->createRulesJson = '[{"days_before":7,"penalty_percent":0},{"days_before":0,"penalty_percent":100}]';
    $policyComponent->createStatus = 'active';
    $policyComponent->createPolicy();

    $policy = CancellationPolicy::query()->where('partner_id', $partner->id)->first();

    expect($policy)->not->toBeNull();

    $policyComponent->startEditing($policy->id);
    $policyComponent->editName = 'Updated policy';
    $policyComponent->editRulesJson = '[{"days_before":0,"penalty_percent":100}]';
    $policyComponent->updatePolicy();

    $policy->refresh();

    expect($policy->name)->toBe('Updated policy');

    $policyComponent->deletePolicy($policy->id);

    expect(CancellationPolicy::withTrashed()->where('id', $policy->id)->whereNotNull('deleted_at')->exists())->toBeTrue();

    $taxComponent = app('livewire')->new('pages::partner.policies.taxes.index');
    $taxComponent->mount();
    $taxComponent->createName = 'VAT';
    $taxComponent->createRate = '0.2';
    $taxComponent->createAppliesTo = 'booking';
    $taxComponent->createInclusive = '0';
    $taxComponent->createStatus = 'active';
    $taxComponent->createTax();

    $tax = Tax::query()->where('partner_id', $partner->id)->first();

    expect($tax)->not->toBeNull();

    $taxComponent->startEditing($tax->id);
    $taxComponent->editRate = '0.1';
    $taxComponent->updateTax();

    $tax->refresh();

    expect((float) $tax->rate)->toBe(0.1);

    $feeComponent = app('livewire')->new('pages::partner.policies.fees.index');
    $feeComponent->mount();
    $feeComponent->createName = 'Cleaning';
    $feeComponent->createAmount = '25.00';
    $feeComponent->createAppliesTo = 'booking';
    $feeComponent->createStatus = 'active';
    $feeComponent->createFee();

    $fee = Fee::query()->where('partner_id', $partner->id)->first();

    expect($fee)->not->toBeNull();

    $feeComponent->startEditing($fee->id);
    $feeComponent->editAmount = '30.00';
    $feeComponent->updateFee();

    $fee->refresh();

    expect((float) $fee->amount)->toBe(30.0);

    $feeComponent->deleteFee($fee->id);

    expect(Fee::query()->where('id', $fee->id)->exists())->toBeFalse();

    $unitShowComponent = app('livewire')->new('pages::partner.catalog.products.units.show');
    $unitShowComponent->mount($product, $unit);
    $unitShowComponent->metaJson = '{"amenities":["wifi","sauna"]}';
    $unitShowComponent->updateUnit();

    $unit->refresh();

    expect($unit->meta)->toMatchArray(['amenities' => ['wifi', 'sauna']]);

    $unitShowComponent->rangeStart = now()->addDays(2)->toDateString();
    $unitShowComponent->rangeEnd = now()->addDays(3)->toDateString();
    $unitShowComponent->rangeAvailable = '0';
    $unitShowComponent->rangeMinStay = '2';
    $unitShowComponent->saveCalendarRange();

    expect($unit->calendars()->count())->toBeGreaterThanOrEqual(2);
});

it('allows partner admins to create accommodations with the setup wizard', function () {
    Role::create(['name' => 'partner-admin']);

    $partner = Partner::factory()->create();
    $user = User::factory()->create(['partner_id' => $partner->id]);
    $user->assignRole('partner-admin');

    $location = Location::factory()->for($partner)->create();
    $policy = CancellationPolicy::factory()->for($partner)->create();

    $this->actingAs($user);

    $component = app('livewire')->new('pages::partner.catalog.accommodations.create');
    $component->mount();
    $component->productName = 'Cliffside Lodge';
    $component->productLocationId = $location->id;
    $component->productDefaultCurrency = 'EUR';
    $component->unitName = 'Suite A';
    $component->unitOccupancyAdults = '2';
    $component->unitOccupancyChildren = '1';
    $component->ratePlanName = 'Standard';
    $component->ratePlanCurrency = 'EUR';
    $component->ratePlanCancellationPolicyId = $policy->id;
    $component->priceStartsOn = now()->toDateString();
    $component->priceEndsOn = now()->addDays(2)->toDateString();
    $component->priceAmount = '120';
    $component->availabilityStart = now()->toDateString();
    $component->availabilityEnd = now()->addDay()->toDateString();
    $component->createAccommodation();

    $product = Product::query()->firstWhere('name', 'Cliffside Lodge');

    expect($product)->not->toBeNull()
        ->and($product->type)->toBe('accommodation');

    expect(Unit::query()->where('product_id', $product->id)->exists())->toBeTrue();
    expect(RatePlan::query()->where('product_id', $product->id)->exists())->toBeTrue();
    expect(RatePlanPrice::query()->whereHas('ratePlan', fn ($query) => $query->where('product_id', $product->id))->exists())
        ->toBeTrue();
    expect(UnitCalendar::query()->whereHas('unit', fn ($query) => $query->where('product_id', $product->id))->count())
        ->toBeGreaterThanOrEqual(2);
});

it('allows partner admins to update rate plans and pricing windows', function () {
    Role::create(['name' => 'partner-admin']);

    $partner = Partner::factory()->create();
    $user = User::factory()->create(['partner_id' => $partner->id]);
    $user->assignRole('partner-admin');

    $product = Product::factory()->for($partner)->create(['type' => 'accommodation']);
    $policy = CancellationPolicy::factory()->for($partner)->create();
    $ratePlan = RatePlan::factory()->for($partner)->for($product)->create([
        'name' => 'Standard',
        'cancellation_policy_id' => $policy->id,
    ]);

    $this->actingAs($user);

    $component = app('livewire')->new('pages::partner.catalog.products.rate-plans.show');
    $component->mount($product, $ratePlan);

    $component->name = 'Updated Plan';
    $component->pricingModel = 'per_person';
    $component->currency = 'USD';
    $component->status = 'active';
    $component->updateRatePlan();

    $ratePlan->refresh();

    expect($ratePlan->name)->toBe('Updated Plan');
    expect($ratePlan->pricing_model)->toBe('per_person');
    expect($ratePlan->currency)->toBe('USD');

    $component->startsOn = now()->addDay()->toDateString();
    $component->endsOn = now()->addDays(3)->toDateString();
    $component->price = '120.50';
    $component->extraAdult = '15.00';
    $component->extraChild = '7.50';
    $component->addPrice();

    $ratePlan->refresh();

    $priceWindow = $ratePlan->prices()->first();

    expect($priceWindow)->not()->toBeNull();

    $component->startEditingPrice($priceWindow->id);
    $component->editPrice = '140.75';
    $component->editExtraAdult = '20.00';
    $component->updatePrice();

    $priceWindow->refresh();

    expect($priceWindow->price)->toBe('140.75');
    expect($priceWindow->extra_adult)->toBe('20.00');

    $component->deletePrice($priceWindow->id);

    expect($ratePlan->prices()->count())->toBe(0);
});

it('allows partner admins to update units and calendar overrides', function () {
    Role::create(['name' => 'partner-admin']);

    $partner = Partner::factory()->create();
    $user = User::factory()->create(['partner_id' => $partner->id]);
    $user->assignRole('partner-admin');

    $product = Product::factory()->for($partner)->create(['type' => 'accommodation']);
    $unit = Unit::factory()->for($partner)->for($product)->create([
        'name' => 'Suite A',
        'occupancy_adults' => 2,
        'occupancy_children' => 1,
    ]);

    $this->actingAs($user);

    $component = app('livewire')->new('pages::partner.catalog.products.units.show');
    $component->mount($product, $unit);

    $component->name = 'Suite B';
    $component->occupancyAdults = '4';
    $component->occupancyChildren = '2';
    $component->housekeepingRequired = '1';
    $component->updateUnit();

    $unit->refresh();

    expect($unit->name)->toBe('Suite B');
    expect($unit->occupancy_adults)->toBe(4);
    expect($unit->occupancy_children)->toBe(2);
    expect($unit->housekeeping_required)->toBeTrue();

    $component->calendarDate = now()->addDays(2)->toDateString();
    $component->calendarAvailable = '0';
    $component->calendarMinStay = '2';
    $component->calendarMaxStay = '5';
    $component->calendarReason = 'Maintenance';
    $component->saveCalendar();

    expect(UnitCalendar::query()->where('unit_id', $unit->id)->count())->toBe(1);
});

it('allows partner admins to add event overrides and blackouts', function () {
    Role::create(['name' => 'partner-admin']);

    $partner = Partner::factory()->create();
    $user = User::factory()->create(['partner_id' => $partner->id]);
    $user->assignRole('partner-admin');

    $product = Product::factory()->for($partner)->create(['type' => 'event']);
    $event = Event::factory()->for($partner)->for($product)->create();
    $location = Location::factory()->for($partner)->create();

    $this->actingAs($user);

    $overrideComponent = app('livewire')->new('pages::partner.availability.events.overrides');
    $overrideComponent->mount($event);
    $overrideComponent->overrideField = 'capacity_total';
    $overrideComponent->overrideValue = '18';
    $overrideComponent->addOverride();

    expect($event->overrides()->count())->toBe(1);

    $blackoutComponent = app('livewire')->new('pages::partner.availability.blackouts.index');
    $blackoutComponent->mount();
    $blackoutComponent->productId = $product->id;
    $blackoutComponent->locationId = $location->id;
    $blackoutComponent->startsAt = now()->addDay()->toDateString();
    $blackoutComponent->endsAt = now()->addDays(2)->toDateString();
    $blackoutComponent->reason = 'Weather risk';
    $blackoutComponent->status = 'active';
    $blackoutComponent->createBlackout();

    expect(EventBlackout::query()->where('partner_id', $partner->id)->count())->toBe(1);
});

it('filters blackouts by product and date range', function () {
    Role::create(['name' => 'partner-admin']);

    $partner = Partner::factory()->create();
    $user = User::factory()->create(['partner_id' => $partner->id]);
    $user->assignRole('partner-admin');

    $productA = Product::factory()->for($partner)->create(['type' => 'event']);
    $productB = Product::factory()->for($partner)->create(['type' => 'event']);
    $location = Location::factory()->for($partner)->create();

    EventBlackout::factory()->for($partner)->create([
        'product_id' => $productA->id,
        'location_id' => $location->id,
        'starts_at' => now()->addDays(2)->toDateString(),
        'ends_at' => now()->addDays(4)->toDateString(),
    ]);

    EventBlackout::factory()->for($partner)->create([
        'product_id' => $productB->id,
        'location_id' => $location->id,
        'starts_at' => now()->addDays(10)->toDateString(),
        'ends_at' => now()->addDays(12)->toDateString(),
    ]);

    $this->actingAs($user);

    $component = app('livewire')->new('pages::partner.availability.blackouts.index');
    $component->mount();
    $component->filterProductId = $productA->id;
    $component->filterDateFrom = now()->addDay()->toDateString();
    $component->filterDateTo = now()->addDays(5)->toDateString();

    $filtered = $component->getBlackoutsProperty();

    expect($filtered->total())->toBe(1);
});

it('blocks users without partner roles from partner pages', function () {
    $partner = Partner::factory()->create();
    $user = User::factory()->create(['partner_id' => $partner->id]);

    $this->actingAs($user)
        ->get('/partner/catalog/products')
        ->assertForbidden();
});

it('blocks partner users without partner context', function () {
    Role::create(['name' => 'partner-admin']);

    $user = User::factory()->create();
    $user->assignRole('partner-admin');

    $this->actingAs($user)
        ->get('/partner/catalog/products')
        ->assertForbidden();
});
