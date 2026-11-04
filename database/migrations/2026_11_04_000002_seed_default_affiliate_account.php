<?php

use App\Models\AffiliateSale;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $affiliate = User::updateOrCreate(
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

        // Tambahkan contoh data penjualan afiliasi jika belum ada
        if ($affiliate->affiliateSales()->count() === 0) {
            AffiliateSale::create([
                'affiliate_id' => $affiliate->id,
                'order_code' => 'ORD-261104-MTR01',
                'customer_name' => 'Rina Melati',
                'customer_phone' => '081388776655',
                'package_name' => 'Paket Best Seller - Complete Radiant Glow',
                'sale_amount' => 299000,
                'commission_rate' => 10.00,
                'commission_amount' => 29900,
                'payment_method' => 'QRIS Resmi URSKYND (Semua M-Banking & E-Wallet)',
                'shipping_address' => 'Jl. Dago No. 45, Bandung',
                'notes' => 'Lunas via QRIS BCA',
                'status' => 'approved',
                'approved_at' => now()->subDay(),
            ]);

            AffiliateSale::create([
                'affiliate_id' => $affiliate->id,
                'order_code' => 'ORD-261104-MTR02',
                'customer_name' => 'Dewi Kartika',
                'customer_phone' => '085711223344',
                'package_name' => 'Paket Ultimate Intensive Flawless Pack',
                'sale_amount' => 389000,
                'commission_rate' => 10.00,
                'commission_amount' => 38900,
                'payment_method' => 'QRIS Resmi URSKYND (Semua M-Banking & E-Wallet)',
                'shipping_address' => 'Jl. Gubeng Kertajaya No. 12, Surabaya',
                'notes' => 'Lunas via QRIS Mandiri',
                'status' => 'approved',
                'approved_at' => now()->subHours(5),
            ]);

            AffiliateSale::create([
                'affiliate_id' => $affiliate->id,
                'order_code' => 'ORD-261104-MTR03',
                'customer_name' => 'Siska Wulandari',
                'customer_phone' => '081233445566',
                'package_name' => 'Paket Glowing Starter',
                'sale_amount' => 185000,
                'commission_rate' => 10.00,
                'commission_amount' => 18500,
                'payment_method' => 'QRIS Resmi URSKYND (Semua M-Banking & E-Wallet)',
                'shipping_address' => 'Jl. Malioboro No. 8, Yogyakarta',
                'notes' => 'Menunggu konfirmasi bukti bayar QRIS',
                'status' => 'pending',
            ]);
        }
    }

    public function down(): void
    {
        User::where('email', 'mitra@urskynd.test')->forceDelete();
    }
};
