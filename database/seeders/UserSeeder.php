<?php

namespace Database\Seeders;

use App\Domains\User\Enums\UserRole;
use App\Domains\User\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name'  => 'Admin',
            'email' => 'admin@email.com',
            'role'  => UserRole::ADMIN->value,
        ]);

        User::factory()->create([
            'name'  => 'External Found',
            'email' => 'external@found.com',
            'role'  => UserRole::EXTERNAL_FOUND->value,
        ]);
    }
}
