<?php

use App\Models\ApiClient;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows a super admin to create partners', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $payload = [
        'name' => 'Acme Adventures',
        'slug' => 'acme-adventures',
        'billing_email' => 'billing@acme.test',
        'currency' => 'EUR',
        'timezone' => 'Europe/Dublin',
        'status' => 'active',
    ];

    $this->actingAs($user)
        ->postJson('/api/v1/admin/partners', $payload)
        ->assertCreated()
        ->assertJsonPath('data.slug', 'acme-adventures');
});

it('filters partners by status and search for admins', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $matching = Partner::factory()->create([
        'name' => 'Acme Adventures',
        'slug' => 'acme-adventures',
        'status' => 'active',
    ]);

    Partner::factory()->create([
        'name' => 'Hidden Adventures',
        'slug' => 'hidden-adventures',
        'status' => 'pending',
    ]);

    $this->actingAs($user)
        ->getJson('/api/v1/admin/partners?status=active&search=acme')
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $matching->id);
});

it('lists only pending partners for admins', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $pending = Partner::factory()->create(['status' => 'pending']);
    Partner::factory()->create(['status' => 'active']);

    $this->actingAs($user)
        ->getJson('/api/v1/admin/partners/pending')
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $pending->id)
        ->assertJsonPath('data.0.status', 'pending');
});

it('allows a partner to sign up and stays pending until approved', function () {
    $response = $this->postJson('/api/v1/front/partners/signup', [
        'name' => 'Wild Atlantic Adventures',
        'billing_email' => 'owners@waa.test',
        'timezone' => 'Europe/Dublin',
    ])->assertCreated();

    $response->assertJsonPath('data.status', 'pending');
    $response->assertJsonPath('data.slug', 'wild-atlantic-adventures');
});

it('auto-uniques partner slugs during signup', function () {
    Partner::factory()->create([
        'name' => 'Wild Atlantic Adventures',
        'slug' => 'wild-atlantic-adventures',
    ]);

    $response = $this->postJson('/api/v1/front/partners/signup', [
        'name' => 'Wild Atlantic Adventures',
        'billing_email' => 'owners@waa.test',
        'timezone' => 'Europe/Dublin',
    ])->assertCreated();

    $response->assertJsonPath('data.slug', 'wild-atlantic-adventures-1');
});

it('allows a super admin to create the initial partner api client', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');
    $partner = Partner::factory()->create();

    $response = $this->actingAs($user)
        ->postJson("/api/v1/admin/partners/{$partner->id}/api-clients", [
            'client_id' => 'waa_bootstrap',
            'scopes' => ['bookings:read'],
        ])
        ->assertCreated();

    $secret = $response->json('client_secret');
    $apiClient = ApiClient::query()->where('client_id', 'waa_bootstrap')->first();

    expect($secret)->not->toBeEmpty();
    expect($apiClient)->not->toBeNull();
    expect(Hash::check($secret, $apiClient?->client_secret_hash))->toBeTrue();
});

it('allows admins to activate partners so api keys work', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');
    $partner = Partner::factory()->create([
        'status' => 'pending',
    ]);
    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('inactive-secret-1234567890')
        ->create([
            'client_id' => 'inactive_partner',
        ]);

    $this->getJson('/api/v1/front/partner', [
        'X-Client-Id' => $apiClient->client_id,
        'X-Client-Secret' => 'inactive-secret-1234567890',
    ])->assertForbidden();

    $this->actingAs($user)
        ->patchJson("/api/v1/admin/partners/{$partner->id}/status", [
            'status' => 'active',
        ])
        ->assertSuccessful()
        ->assertJsonPath('data.status', 'active');

    $this->getJson('/api/v1/front/partner', [
        'X-Client-Id' => $apiClient->client_id,
        'X-Client-Secret' => 'inactive-secret-1234567890',
    ])->assertSuccessful()
        ->assertJsonPath('data.id', $partner->id);
});

it('allows partner api keys to create additional api clients', function () {
    $partner = Partner::factory()->create();
    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-1234567890')
        ->create([
            'client_id' => 'partner_key',
        ]);

    $payload = [
        'client_id' => 'acme_partner',
        'scopes' => ['bookings:read', 'bookings:write'],
    ];

    $response = $this->postJson('/api/v1/partner/api-clients', $payload, [
        'X-Client-Id' => $apiClient->client_id,
        'X-Client-Secret' => 'partner-secret-1234567890',
    ])->assertCreated();

    $secret = $response->json('client_secret');
    $newClient = ApiClient::query()->where('client_id', 'acme_partner')->first();

    expect($secret)->not->toBeEmpty();
    expect($newClient)->not->toBeNull();
    expect(Hash::check($secret, $newClient?->client_secret_hash))->toBeTrue();
});

it('resolves partner context for front api key requests', function () {
    $partner = Partner::factory()->create();
    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('front-secret-1234567890')
        ->create([
            'client_id' => 'front_partner',
        ]);

    $this->getJson('/api/v1/front/partner', [
        'X-Client-Id' => $apiClient->client_id,
        'X-Client-Secret' => 'front-secret-1234567890',
    ])->assertSuccessful()
        ->assertJsonPath('data.id', $partner->id);
});

it('blocks api key access for inactive partners', function () {
    $partner = Partner::factory()->create([
        'status' => 'pending',
    ]);
    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('pending-secret-1234567890')
        ->create([
            'client_id' => 'pending_partner',
        ]);

    $this->getJson('/api/v1/front/partner', [
        'X-Client-Id' => $apiClient->client_id,
        'X-Client-Secret' => 'pending-secret-1234567890',
    ])->assertForbidden();
});

it('enforces idempotency keys for admin mutations', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $payload = [
        'name' => 'Idempotent Partner',
        'slug' => 'idempotent-partner',
        'billing_email' => 'billing@idempotent.test',
        'currency' => 'EUR',
        'timezone' => 'Europe/Dublin',
        'status' => 'active',
    ];

    $headers = [
        'Idempotency-Key' => 'admin-partner-key',
    ];

    $first = $this->actingAs($user)
        ->postJson('/api/v1/admin/partners', $payload, $headers)
        ->assertCreated()
        ->json('data.id');

    $second = $this->actingAs($user)
        ->postJson('/api/v1/admin/partners', $payload, $headers)
        ->assertSuccessful()
        ->json('data.id');

    expect($first)->toBe($second);
    expect(Partner::query()->where('slug', 'idempotent-partner')->count())->toBe(1);

    $this->actingAs($user)
        ->postJson('/api/v1/admin/partners', array_merge($payload, [
            'slug' => 'different-partner',
        ]), $headers)
        ->assertStatus(409);
});
