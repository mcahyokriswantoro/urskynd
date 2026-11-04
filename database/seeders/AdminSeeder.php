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

        User::updateOrCreate(
            ['email' => 'mitra@urskynd.test'],
            [
                'name' => 'Nadia Putri (Mitra URSKYND)',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_affiliate' => true,
                'affiliate_code' => 'URSMITRA01',
                'affiliate_status' => 'active',
                'commission_rate' => 10.00,
                'commission_balance' => 68800,
                'total_commission_earned' => 68800,
                'bank_name' => 'BCA',
                'bank_account_number' => '8810293847',
                'bank_account_holder' => 'Nadia Putri',
                'phone' => '081298765432',
                'address' => 'Jl. Kemang Raya No. 25, Jakarta Selatan',
                'status' => 'active',
                'birth_date' => '1999-08-20',
                'gender' => 'female',
                'onboarding_completed' => true,
                'email_verified_at' => now(),
                'points' => 100,
            ]
        );
    }
}
