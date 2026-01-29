<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::findOrCreate('super-admin');

        $user = User::query()->firstOrCreate(
            ['email' => 'admin@waa.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        if (! $user->hasRole($role)) {
            $user->assignRole($role);
        }
    }
}
