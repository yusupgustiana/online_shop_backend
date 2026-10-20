<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 9 user biasa
        User::factory(9)->create([
            'roles' => 'USER',
        ]);

        // Admin
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
            'phone' => '123-456-7890',
            'roles' => 'ADMIN',
        ]);

        // User khusus untuk login
        User::factory()->create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('12345678'),
            'phone' => '081234567890',
            'roles' => 'USER',
        ]);
    }
}