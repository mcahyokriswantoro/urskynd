<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin URSKYND',
            'email' => 'admin@urskynd.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'onboarding_completed' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Sarah Amelia',
            'email' => 'user@urskynd.test',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'active',
            'birth_date' => '1998-05-15',
            'gender' => 'female',
            'onboarding_completed' => true,
            'email_verified_at' => now(),
            'points' => 150,
        ]);
    }
}
