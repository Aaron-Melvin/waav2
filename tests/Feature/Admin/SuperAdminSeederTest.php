<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('seeds a super admin user with the correct role', function () {
    $this->seed(\Database\Seeders\SuperAdminSeeder::class);

    $user = User::query()->where('email', 'admin@waa.test')->first();

    expect($user)->not->toBeNull();
    expect(Hash::check('password', $user?->password))->toBeTrue();
    expect($user?->hasRole('super-admin'))->toBeTrue();
});
