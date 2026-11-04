<?php

namespace App\Http\Controllers;

use App\Models\AffiliateSale;
use App\Models\AffiliateWithdrawal;
use App\Models\Product;
use App\Models\User;
use App\Support\AffiliateSettingsDefaults;
use App\Support\PurchasePageDefaults;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AffiliateController extends Controller
{
    /**
     * Tampilkan dashboard Mitra / Afiliator (gaya Shopee Affiliate: Link Per Produk + Pelacakan Klik & Konversi Otomatis).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $sales = $user->affiliateSales()->with('product')->get();
        $withdrawals = $user->affiliateWithdrawals()->get();
        $clicks = Schema::hasTable('affiliate_clicks') ? $user->affiliateClicks()->get() : collect();

        $totalClicks = $clicks->count();
        $totalOrders = $sales->whereIn('status', ['pending', 'approved'])->count();
        $conversionRate = $totalClicks > 0 ? round(($totalOrders / $totalClicks) * 100, 1) : 0;

        $stats = [
            'balance' => (int) $user->commission_balance,
            'total_earned' => (int) $user->total_commission_earned,
            'pending_commission' => (int) $sales->where('status', 'pending')->sum('commission_amount'),
            'approved_sales_count' => $sales->where('status', 'approved')->count(),
            'pending_sales_count' => $sales->where('status', 'pending')->count(),
            'total_withdrawn' => (int) $withdrawals->where('status', 'approved')->sum('amount'),
            'pending_withdrawal' => (int) $withdrawals->where('status', 'pending')->sum('amount'),
            'total_clicks' => $totalClicks,
            'conversion_rate' => $conversionRate,
        ];

        // Daftar Produk Katalog dengan Link Afiliasi Per Produk & Statistik Klik/Terjual masing-masing
        $affiliateProducts = Product::with('category')
            ->where('status', 'active')
            ->get()
            ->map(function (Product $product) use ($user, $sales, $clicks) {
                $price = (int) ($product->price ?? 129000);
                $rate = (float) ($product->commission_rate ?: $user->commission_rate ?: 10);
                $commissionPerUnit = (int) round($price * ($rate / 100));

                $productClicks = $clicks->where('product_id', $product->id)->count();
                $productSales = $sales->where('product_id', $product->id)->whereIn('status', ['pending', 'approved']);
                $productEarned = (int) $sales->where('product_id', $product->id)->where('status', 'approved')->sum('commission_amount');

                $affiliateUrl = $user->affiliate_code
                    ? route('products.show', ['product' => $product->slug, 'ref' => $user->affiliate_code])
                    : route('products.show', $product->slug);

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'category' => $product->category->name ?? 'Skincare',
                    'image_url' => $product->image_url,
                    'price' => $price,
                    'formatted_price' => PurchasePageDefaults::rupiah($price),
                    'commission_rate' => $rate,
                    'commission_amount' => $commissionPerUnit,
                    'formatted_commission' => PurchasePageDefaults::rupiah($commissionPerUnit),
                    'affiliate_url' => $affiliateUrl,
                    'clicks_count' => $productClicks,
                    'orders_count' => (int) $productSales->sum('quantity'),
                    'earned_commission' => PurchasePageDefaults::rupiah($productEarned),
                ];
            });

        $purchaseContent = AdminPurchasePageController::content();
        $packages = collect($purchaseContent['packages'] ?? [])->map(function ($pkg) use ($user) {
            $price = (int) ($pkg['price'] ?? 0);
            $rate = (float) ($user->commission_rate ?: 10);
            $estCommission = (int) round($price * ($rate / 100));
            return [
                'name' => $pkg['name'],
                'price' => $price,
                'formatted_price' => PurchasePageDefaults::rupiah($price),
                'estimated_commission' => $estCommission,
                'formatted_commission' => PurchasePageDefaults::rupiah($estCommission),
            ];
        })->all();

        $affSettings = AffiliateSettingsDefaults::current();

        return view('affiliate.index', compact('user', 'sales', 'withdrawals', 'stats', 'affiliateProducts', 'packages', 'affSettings'));
    }

    /**
     * Aktifkan status Mitra / Afiliator bagi akun member reguler yang sudah terdaftar.
     */
    public function join(Request $request)
    {
        $user = $request->user();
        $affSettings = AffiliateSettingsDefaults::current();

        $request->validate([
            'affiliate_code' => ['nullable', 'string', 'alpha_num', 'min:4', 'max:20', Rule::unique('users', 'affiliate_code')->ignore($user->id)],
            'bank_name' => ['required', 'string', 'max:60'],
            'bank_account_number' => ['required', 'string', 'max:60'],
            'bank_account_holder' => ['required', 'string', 'max:120'],
        ], [
            'affiliate_code.unique' => 'Kode afiliasi sudah dipakai mitra lain, silakan pilih kode lain.',
            'affiliate_code.alpha_num' => 'Kode afiliasi hanya boleh huruf dan angka tanpa spasi.',
        ]);

        $user->update([
            'is_affiliate' => true,
            'affiliate_code' => User::generateUniqueAffiliateCode($user->name, $request->affiliate_code),
            'affiliate_status' => 'active',
            'commission_rate' => $user->commission_rate ?: (float) ($affSettings['default_commission_rate'] ?? 10.00),
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_holder' => $request->bank_account_holder,
        ]);

        return redirect()->route('affiliate.index')->with('success', 'Selamat! Akun Anda kini resmi terdaftar sebagai Mitra / Afiliator URSKYND dengan kode: ' . $user->affiliate_code);
    }

    /**
     * Perbarui data rekening pencairan & kode afiliasi.
     */
    public function updateBank(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'affiliate_code' => ['required', 'string', 'alpha_num', 'min:4', 'max:20', Rule::unique('users', 'affiliate_code')->ignore($user->id)],
            'bank_name' => ['required', 'string', 'max:60'],
            'bank_account_number' => ['required', 'string', 'max:60'],
            'bank_account_holder' => ['required', 'string', 'max:120'],
        ], [
            'affiliate_code.unique' => 'Kode afiliasi sudah dipakai mitra lain.',
            'affiliate_code.alpha_num' => 'Kode afiliasi hanya boleh huruf dan angka tanpa spasi.',
        ]);

        $user->update([
            'affiliate_code' => strtoupper($request->affiliate_code),
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_holder' => $request->bank_account_holder,
        ]);

        return back()->with('success', 'Data rekening pencairan dan kode afiliasi berhasil diperbarui.');
    }

    /**
     * Ajukan pencairan saldo komisi (Withdraw).
     */
    public function requestWithdrawal(Request $request)
    {
        $user = $request->user();

        if (!$user->isAffiliate() || $user->affiliate_status !== 'active') {
            return back()->withErrors(['amount' => 'Akun mitra Anda tidak aktif.']);
        }

        $affSettings = AffiliateSettingsDefaults::current();
        $minWithdraw = (int) ($affSettings['min_withdrawal_amount'] ?? 20000);

        $request->validate([
            'amount' => ['required', 'integer', 'min:' . $minWithdraw, 'max:' . max(0, (int) $user->commission_balance)],
            'bank_name' => ['required', 'string', 'max:60'],
            'bank_account_number' => ['required', 'string', 'max:60'],
            'bank_account_holder' => ['required', 'string', 'max:120'],
        ], [
            'amount.min' => 'Minimal pencairan komisi adalah Rp ' . number_format($minWithdraw, 0, ',', '.') . '.',
            'amount.max' => 'Nominal pencairan melebihi saldo komisi siap cair Anda.',
        ]);

        $amount = (int) $request->amount;

        DB::transaction(function () use ($user, $request, $amount) {
            $freshUser = User::lockForUpdate()->find($user->id);

            if ($freshUser->commission_balance < $amount) {
                abort(422, 'Saldo komisi tidak mencukupi.');
            }

            $freshUser->update([
                'commission_balance' => $freshUser->commission_balance - $amount,
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_account_holder' => $request->bank_account_holder,
            ]);

            AffiliateWithdrawal::create([
                'affiliate_id' => $freshUser->id,
                'withdrawal_code' => 'WD-' . now()->format('ymd') . '-' . strtoupper(substr(uniqid(), -5)),
                'amount' => $amount,
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_account_holder' => $request->bank_account_holder,
                'status' => 'pending',
            ]);
        });

        return back()->with('success', 'Permintaan pencairan komisi sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil diajukan! Admin akan segera memproses transfer ke rekening Anda.');
    }
}
