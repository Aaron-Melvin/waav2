<?php

use App\Models\CancellationPolicy;
use App\Models\Fee;
use App\Models\Partner;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows super admins to list pricing policies', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $tax = Tax::factory()->create();
    $fee = Fee::factory()->create();
    $policy = CancellationPolicy::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/v1/admin/taxes')
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $tax->id);

    $this->actingAs($user)
        ->getJson('/api/v1/admin/fees')
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $fee->id);

    $this->actingAs($user)
        ->getJson('/api/v1/admin/cancellation-policies')
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $policy->id);
});

it('allows super admins to create and update pricing policies', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');
    $partnerId = Partner::factory()->create()->id;

    $tax = $this->actingAs($user)
        ->postJson('/api/v1/admin/taxes', [
            'partner_id' => $partnerId,
            'name' => 'VAT',
            'rate' => 0.2,
            'applies_to' => 'booking',
        ])
        ->assertCreated()
        ->json('data.id');

    $this->actingAs($user)
        ->patchJson("/api/v1/admin/taxes/{$tax}", [
            'name' => 'VAT Updated',
            'rate' => 0.25,
            'applies_to' => 'booking',
            'is_inclusive' => false,
            'status' => 'active',
        ])
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'VAT Updated');

    $fee = $this->actingAs($user)
        ->postJson('/api/v1/admin/fees', [
            'partner_id' => $partnerId,
            'name' => 'Service Fee',
            'amount' => 5,
            'applies_to' => 'booking',
        ])
        ->assertCreated()
        ->json('data.id');

    $this->actingAs($user)
        ->patchJson("/api/v1/admin/fees/{$fee}", [
            'name' => 'Service Fee Updated',
            'type' => 'flat',
            'amount' => 6,
            'applies_to' => 'booking',
            'status' => 'active',
        ])
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Service Fee Updated');

    $policy = $this->actingAs($user)
        ->postJson('/api/v1/admin/cancellation-policies', [
            'partner_id' => $partnerId,
            'name' => 'Flexible',
            'rules' => [
                ['window_hours' => 24, 'fee_percent' => 10],
            ],
        ])
        ->assertCreated()
        ->json('data.id');

    $this->actingAs($user)
        ->patchJson("/api/v1/admin/cancellation-policies/{$policy}", [
            'name' => 'Flexible Updated',
            'rules' => [
                ['window_hours' => 12, 'fee_percent' => 20],
            ],
            'status' => 'active',
        ])
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Flexible Updated');
});
