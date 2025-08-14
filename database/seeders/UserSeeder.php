<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@securesign.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'can_sign' => true,
            'can_review' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Boss User
        User::create([
            'name' => 'CEO Boss',
            'email' => 'boss@securesign.com',
            'password' => Hash::make('password'),
            'role' => 'boss',
            'can_sign' => true,
            'can_review' => false,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Manager User
        User::create([
            'name' => 'Department Manager',
            'email' => 'manager@securesign.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'can_sign' => false,
            'can_review' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Staff Users
        User::create([
            'name' => 'John Smith',
            'email' => 'john@securesign.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'can_sign' => false,
            'can_review' => false,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@securesign.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'can_sign' => false,
            'can_review' => false,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}