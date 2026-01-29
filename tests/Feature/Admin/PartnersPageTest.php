<?php

use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows super admins to access the partner approvals page', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $this->actingAs($user)
        ->get('/admin/partners')
        ->assertSuccessful()
        ->assertSee('Partner approvals');
});

it('blocks non-admin users from the partner approvals page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/partners')
        ->assertForbidden();
});

it('allows super admins to access partner detail pages', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $partner = Partner::factory()->create();

    $this->actingAs($user)
        ->get("/admin/partners/{$partner->id}")
        ->assertSuccessful()
        ->assertSee($partner->name);
});

it('blocks non-admin users from partner detail pages', function () {
    $user = User::factory()->create();
    $partner = Partner::factory()->create();

    $this->actingAs($user)
        ->get("/admin/partners/{$partner->id}")
        ->assertForbidden();
});

it('allows super admins to issue api clients for partners', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $partner = Partner::factory()->create();

    $this->actingAs($user);

    $component = app('livewire')->new('pages::admin.partners.show');
    $component->mount($partner);

    $component->clientId = 'partner-portal';
    $component->clientSecret = 'super-secret-token-12345';
    $component->scopes = 'bookings:read, bookings:write';
    $component->status = 'active';

    expect($component->clientId)->toBe('partner-portal');

    $component->issueApiClient();

    expect($component->issuedSecret)->toBe('super-secret-token-12345');
    expect($partner->apiClients()->where('client_id', 'partner-portal')->exists())->toBeTrue();
});
