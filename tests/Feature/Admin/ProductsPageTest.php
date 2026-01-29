<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows super admins to access the products overview page', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $this->actingAs($user)
        ->get('/admin/products')
        ->assertSuccessful()
        ->assertSee('Products');
});

it('blocks non-admin users from the products overview page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/products')
        ->assertForbidden();
});

it('allows super admins to access product detail pages', function () {
    Role::create(['name' => 'super-admin']);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $product = Product::factory()->create();

    $this->actingAs($user)
        ->get("/admin/products/{$product->id}")
        ->assertSuccessful()
        ->assertSee($product->name);
});

it('blocks non-admin users from product detail pages', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $this->actingAs($user)
        ->get("/admin/products/{$product->id}")
        ->assertForbidden();
});
