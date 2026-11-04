<?php

namespace App\Http\Controllers;

use App\Models\AffiliateClick;
use App\Models\AffiliateSale;
use App\Models\AffiliateWithdrawal;
use App\Models\PageContent;
use App\Models\Product;
use App\Models\User;
use App\Support\AffiliateSettingsDefaults;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminAffiliateController extends Controller
{
    /**
     * Tampilkan halaman manajemen Mitra Afiliator, Penjualan & Pencairan Komisi di Panel Admin.
     */
    public function index()
    {
        $affiliates = User::where('is_affiliate', true)
            ->withCount([
                'affiliateSales as total_sales_count',
                'affiliateSales as approved_sales_count' => fn ($q) => $q->where('status', 'approved'),
                'affiliateClicks as total_clicks_count',
            ])
            ->latest()
            ->get();

        $sales = AffiliateSale::with(['affiliate', 'product'])->latest()->get();
        $withdrawals = AffiliateWithdrawal::with('affiliate')->latest()->get();
        $products = Product::withCount([
            'affiliateClicks as clicks_count',
            'affiliateSales as sales_count' => fn ($q) => $q->whereIn('status', ['pending', 'approved']),
        ])->orderBy('name')->get();

        $stats = [
            'total_affiliates' => $affiliates->count(),
            'active_affiliates' => $affiliates->where('affiliate_status', 'active')->count(),
            'total_clicks' => Schema::hasTable('affiliate_clicks') ? AffiliateClick::count() : 0,
            'pending_sales_count' => $sales->where('status', 'pending')->count(),
            'approved_sales_amount' => (int) $sales->where('status', 'approved')->sum('sale_amount'),
            'total_commission_accrued' => (int) $sales->where('status', 'approved')->sum('commission_amount'),
            'pending_withdrawals_count' => $withdrawals->where('status', 'pending')->count(),
            'pending_withdrawals_amount' => (int) $withdrawals->where('status', 'pending')->sum('amount'),
            'approved_withdrawals_amount' => (int) $withdrawals->where('status', 'approved')->sum('amount'),
        ];

        $packages = AdminPurchasePageController::content()['packages'] ?? [];
        $settings = AffiliateSettingsDefaults::current();

        return view('admin.affiliates.index', compact('affiliates', 'sales', 'withdrawals', 'products', 'stats', 'packages', 'settings'));
    }

    /**
     * Simpan pengaturan teks, keuntungan pendaftaran, dan persentase komisi default Mitra/Afiliator.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'default_commission_rate' => ['required', 'numeric', 'min:1', 'max:50'],
            'min_withdrawal_amount' => ['required', 'integer', 'min:1000'],
            'cookie_days' => ['required', 'integer', 'min:1', 'max:365'],
            'register_badge' => ['required', 'string', 'max:50'],
            'register_title' => ['required', 'string', 'max:120'],
            'register_subtitle' => ['required', 'string', 'max:255'],
            'register_benefits_title' => ['required', 'string', 'max:150'],
            'register_benefits' => ['required', 'string', 'max:2000'],
            'dashboard_badge' => ['required', 'string', 'max:80'],
            'dashboard_title' => ['required', 'string', 'max:120'],
            'dashboard_subtitle' => ['required', 'string', 'max:300'],
            'catalog_title' => ['required', 'string', 'max:150'],
            'catalog_subtitle' => ['required', 'string', 'max:350'],
            'bundle_banner_badge' => ['required', 'string', 'max:150'],
            'bundle_banner_text' => ['required', 'string', 'max:300'],
            'how_it_works_title' => ['required', 'string', 'max:120'],
            'how_it_works_subtitle' => ['required', 'string', 'max:255'],
            'step_1_title' => ['required', 'string', 'max:120'],
            'step_1_desc' => ['required', 'string', 'max:400'],
            'step_2_title' => ['required', 'string', 'max:120'],
            'step_2_desc' => ['required', 'string', 'max:400'],
            'step_3_title' => ['required', 'string', 'max:120'],
            'step_3_desc' => ['required', 'string', 'max:400'],
            'apply_rate_to_all' => ['nullable', 'boolean'],
        ]);

        $rate = (float) $validated['default_commission_rate'];
        $applyAll = $request->boolean('apply_rate_to_all');
        unset($validated['apply_rate_to_all']);

        PageContent::saveContent(AffiliateSettingsDefaults::KEY, $validated);

        if ($applyAll) {
            User::where('is_affiliate', true)->update(['commission_rate' => $rate]);
            Product::query()->update(['commission_rate' => $rate]);
        }

        return redirect()->route('admin.affiliates.index', ['tab' => 'settings'])
            ->with('success', 'Pengaturan teks, keuntungan pendaftaran, dan persentase komisi berhasil disimpan!');
    }

    /**
     * Update persentase komisi per produk di katalog.
     */
    public function updateProductCommission(Request $request, Product $product)
    {
        $validated = $request->validate([
            'commission_rate' => ['required', 'numeric', 'min:1', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $product->update([
            'price' => (int) $validated['price'],
            'commission_rate' => (float) $validated['commission_rate'],
        ]);

        return back()->with('success', 'Komisi produk "' . $product->name . '" berhasil diperbarui menjadi ' . $product->commission_rate . '% (' . $product->formatted_commission . ' / pcs).');
    }

    /**
     * Catat penjualan afiliasi baru oleh Admin (bisa langsung disetujui & menambah saldo komisi mitra).
     */
    public function storeSale(Request $request)
    {
        $validated = $request->validate([
            'affiliate_id' => ['required', 'exists:users,id'],
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'package_name' => ['required', 'string', 'max:150'],
            'sale_amount' => ['required', 'integer', 'min:1000'],
            'notes' => ['nullable', 'string', 'max:500'],
            'auto_approve' => ['nullable', 'boolean'],
        ]);

        $affiliate = User::findOrFail($validated['affiliate_id']);
        $saleAmount = (int) $validated['sale_amount'];
        $rate = (float) ($affiliate->commission_rate ?: 10);
        $commissionAmount = (int) round($saleAmount * ($rate / 100));
        $approveNow = $request->boolean('auto_approve', true);

        DB::transaction(function () use ($affiliate, $validated, $saleAmount, $rate, $commissionAmount, $approveNow) {
            AffiliateSale::create([
                'affiliate_id' => $affiliate->id,
                'order_code' => 'ORD-' . now()->format('ymd') . '-' . strtoupper(substr(uniqid(), -5)),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'package_name' => $validated['package_name'],
                'sale_amount' => $saleAmount,
                'commission_rate' => $rate,
                'commission_amount' => $commissionAmount,
                'payment_method' => 'QRIS Resmi URSKYND',
                'notes' => $validated['notes'] ?? null,
                'status' => $approveNow ? 'approved' : 'pending',
                'approved_at' => $approveNow ? now() : null,
            ]);

            if ($approveNow) {
                $affiliate->increment('commission_balance', $commissionAmount);
                $affiliate->increment('total_commission_earned', $commissionAmount);
            }
        });

        return back()->with('success', 'Penjualan mitra ' . $affiliate->name . ' berhasil dicatat' . ($approveNow ? ' dan komisi Rp ' . number_format($commissionAmount, 0, ',', '.') . ' telah masuk ke saldo mitra!' : '.'));
    }

    /**
     * Setujui atau tolak penjualan afiliasi (otomatis menyesuaikan saldo komisi mitra).
     */
    public function updateSaleStatus(Request $request, AffiliateSale $sale)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $sale->status;

        if ($newStatus === $oldStatus) {
            return back();
        }

        DB::transaction(function () use ($sale, $oldStatus, $newStatus) {
            $affiliate = User::lockForUpdate()->findOrFail($sale->affiliate_id);
            $commission = (int) $sale->commission_amount;

            // Jika sebelumnya belum approved dan sekarang approved -> tambah saldo komisi
            if ($oldStatus !== 'approved' && $newStatus === 'approved') {
                $affiliate->update([
                    'commission_balance' => $affiliate->commission_balance + $commission,
                    'total_commission_earned' => $affiliate->total_commission_earned + $commission,
                ]);
                $sale->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                ]);
            }
            // Jika sebelumnya sudah approved lalu dibatalkan/ditolak -> kurangi saldo komisi
            elseif ($oldStatus === 'approved' && $newStatus !== 'approved') {
                $affiliate->update([
                    'commission_balance' => max(0, $affiliate->commission_balance - $commission),
                    'total_commission_earned' => max(0, $affiliate->total_commission_earned - $commission),
                ]);
                $sale->update([
                    'status' => $newStatus,
                    'approved_at' => null,
                ]);
            } else {
                $sale->update(['status' => $newStatus]);
            }
        });

        $msg = $newStatus === 'approved'
            ? 'Penjualan ' . $sale->order_code . ' disetujui! Komisi ' . $sale->formatted_commission_amount . ' telah ditambahkan ke saldo ' . ($sale->affiliate->name ?? 'Mitra') . '.'
            : 'Status penjualan ' . $sale->order_code . ' diperbarui menjadi ' . strtoupper($newStatus) . '.';

        return back()->with('success', $msg);
    }

    /**
     * Proses pencairan komisi (Approve = sudah ditransfer, Reject = tolak & kembalikan saldo mitra).
     */
    public function updateWithdrawalStatus(Request $request, AffiliateWithdrawal $withdrawal)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($withdrawal->status !== 'pending') {
            return back()->withErrors(['withdrawal' => 'Permintaan pencairan ini sudah diproses sebelumnya.']);
        }

        $newStatus = $validated['status'];

        DB::transaction(function () use ($withdrawal, $newStatus, $validated) {
            $affiliate = User::lockForUpdate()->findOrFail($withdrawal->affiliate_id);

            if ($newStatus === 'rejected') {
                // Kembalikan saldo komisi ke akun mitra
                $affiliate->update([
                    'commission_balance' => $affiliate->commission_balance + (int) $withdrawal->amount,
                ]);
            }

            $withdrawal->update([
                'status' => $newStatus,
                'admin_notes' => $validated['admin_notes'] ?? ($newStatus === 'approved' ? 'Komisi telah ditransfer ke rekening mitra.' : 'Permintaan pencairan ditolak, saldo dikembalikan.'),
                'processed_at' => now(),
            ]);
        });

        $msg = $newStatus === 'approved'
            ? 'Pencairan komisi ' . $withdrawal->withdrawal_code . ' (' . $withdrawal->formatted_amount . ') berhasil ditandai SUDAH DITRANSFER.'
            : 'Pencairan komisi ' . $withdrawal->withdrawal_code . ' ditolak dan saldo telah dikembalikan ke akun mitra.';

        return back()->with('success', $msg);
    }

    /**
     * Update pengaturan komisi, kode afiliasi, status, dan rekening mitra.
     */
    public function updateAffiliate(Request $request, User $user)
    {
        $validated = $request->validate([
            'affiliate_code' => ['required', 'string', 'alpha_num', 'min:4', 'max:20', Rule::unique('users', 'affiliate_code')->ignore($user->id)],
            'commission_rate' => ['required', 'numeric', 'min:1', 'max:50'],
            'affiliate_status' => ['required', 'in:active,suspended'],
            'bank_name' => ['nullable', 'string', 'max:60'],
            'bank_account_number' => ['nullable', 'string', 'max:60'],
            'bank_account_holder' => ['nullable', 'string', 'max:120'],
        ]);

        $user->update([
            'is_affiliate' => true,
            'affiliate_code' => strtoupper($validated['affiliate_code']),
            'commission_rate' => (float) $validated['commission_rate'],
            'affiliate_status' => $validated['affiliate_status'],
            'bank_name' => $validated['bank_name'] ?? $user->bank_name,
            'bank_account_number' => $validated['bank_account_number'] ?? $user->bank_account_number,
            'bank_account_holder' => $validated['bank_account_holder'] ?? $user->bank_account_holder,
        ]);

        return back()->with('success', 'Pengaturan mitra ' . $user->name . ' (Komisi ' . $user->commission_rate . '%) berhasil diperbarui.');
    }
}

