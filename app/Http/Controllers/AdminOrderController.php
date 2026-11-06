<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PageContent;
use App\Models\ShippingZone;
use App\Support\ShippingRateService;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'orders');
        $statusFilter = $request->query('status', 'all');
        $search = trim((string) $request->query('search', ''));

        $allOrders = Order::with(['user', 'items.product', 'affiliate', 'shippingZone'])
            ->latest()
            ->get();

        $stats = [
            'total_orders' => $allOrders->count(),
            'pending_payment' => $allOrders->where('status', Order::STATUS_PENDING_PAYMENT)->count(),
            'paid_ready' => $allOrders->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING])->count(),
            'shipped' => $allOrders->whereIn('status', [Order::STATUS_SHIPPED, Order::STATUS_DELIVERED])->count(),
            'completed' => $allOrders->where('status', Order::STATUS_COMPLETED)->count(),
            'cancelled' => $allOrders->where('status', Order::STATUS_CANCELLED)->count(),
            'total_revenue' => (int) $allOrders->where('payment_status', 'paid')->sum('total'),
            'total_shipping_collected' => (int) $allOrders->where('payment_status', 'paid')->sum('shipping_cost'),
        ];

        $orders = $allOrders->filter(function (Order $order) use ($statusFilter, $search) {
            if ($statusFilter !== 'all') {
                if ($statusFilter === 'need_process' && !in_array($order->status, [Order::STATUS_PAID, Order::STATUS_PROCESSING], true)) {
                    return false;
                } elseif ($statusFilter !== 'need_process' && $order->status !== $statusFilter) {
                    return false;
                }
            }

            if ($search !== '') {
                $haystack = strtolower($order->order_number . ' ' . $order->recipient_name . ' ' . $order->recipient_phone . ' ' . $order->city . ' ' . $order->tracking_number);
                if (!str_contains($haystack, strtolower($search))) {
                    return false;
                }
            }

            return true;
        })->values();

        $shippingZones = ShippingZone::orderBy('sort_order')->get();
        $gatewaySettings = ShippingRateService::gatewaySettings();

        return view('admin.orders.index', compact(
            'orders',
            'stats',
            'tab',
            'statusFilter',
            'search',
            'shippingZones',
            'gatewaySettings'
        ));
    }

    /**
     * Update status pesanan oleh Admin:
     * - Pembayaran Diterima (paid)
     * - Sedang Dikemas (processing)
     * - Barang Dikirim + No. Resi J&T (shipped)
     * - Paket Sampai (delivered)
     * - Pesanan Selesai (completed)
     * - Dibatalkan (cancelled)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending_payment,paid,processing,shipped,delivered,completed,cancelled'],
            'tracking_number' => ['nullable', 'string', 'max:60'],
            'courier' => ['nullable', 'string', 'max:60'],
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $newStatus = $validated['status'];
        $adminId = $request->user()->id;

        if ($newStatus === Order::STATUS_PAID) {
            $order->markAsPaid($order->payment_method ?: 'Verifikasi Admin', null, $adminId);
            if (!empty($validated['admin_notes'])) {
                $order->update(['admin_notes' => $validated['admin_notes']]);
            }
            return back()->with('success', '✅ Pesanan ' . $order->order_number . ' ditandai PEMBAYARAN DITERIMA! Komisi afiliasi (jika ada) otomatis disetujui.');
        }

        // Jika status maju ke dikemas/dikirim/sampai/selesai dan belum paid, otomatis tandai lunas juga
        if (in_array($newStatus, [Order::STATUS_PROCESSING, Order::STATUS_SHIPPED, Order::STATUS_DELIVERED, Order::STATUS_COMPLETED], true) && $order->payment_status !== 'paid') {
            $order->markAsPaid($order->payment_method ?: 'Verifikasi Admin', null, $adminId);
        }

        $tracking = trim((string) ($validated['tracking_number'] ?? $order->tracking_number));
        if ($newStatus === Order::STATUS_SHIPPED && $tracking === '') {
            $tracking = 'JP' . now()->format('ymd') . rand(100000, 999999);
        }

        $updates = [
            'status' => $newStatus,
            'courier' => $validated['courier'] ?? $order->courier ?: 'J&T Express',
            'tracking_number' => $tracking ?: $order->tracking_number,
            'admin_notes' => $validated['admin_notes'] ?? $order->admin_notes,
        ];

        if ($newStatus === Order::STATUS_SHIPPED && !$order->shipped_at) {
            $updates['shipped_at'] = now();
        }
        if ($newStatus === Order::STATUS_DELIVERED && !$order->delivered_at) {
            $updates['delivered_at'] = now();
        }
        if ($newStatus === Order::STATUS_COMPLETED && !$order->completed_at) {
            $updates['completed_at'] = now();
            $updates['delivered_at'] = $order->delivered_at ?: now();
        }
        if ($newStatus === Order::STATUS_CANCELLED && !$order->cancelled_at) {
            $updates['cancelled_at'] = now();
            $updates['cancel_reason'] = $validated['admin_notes'] ?? 'Dibatalkan oleh Admin.';
        }

        $order->update($updates);

        $historyMap = [
            Order::STATUS_PROCESSING => [
                'Pesanan Sedang Dikemas di Gudang Lamongan',
                'Produk sedang melalui proses quality control dan pengemasan bubble wrap berlapis.',
            ],
            Order::STATUS_SHIPPED => [
                'Barang Dikirim via ' . $updates['courier'] . ' (No. Resi: ' . $updates['tracking_number'] . ')',
                $validated['admin_notes'] ?: ('Paket telah diserahkan ke kurir ' . $updates['courier'] . ' cabang Lamongan menuju ' . $order->city . '.'),
            ],
            Order::STATUS_DELIVERED => [
                'Paket Telah Tiba di Alamat Tujuan (' . $order->city . ')',
                $validated['admin_notes'] ?: 'Kurir melaporkan paket telah tiba di alamat penerima.',
            ],
            Order::STATUS_COMPLETED => [
                'Pesanan Selesai',
                $validated['admin_notes'] ?: 'Transaksi pesanan telah selesai.',
            ],
            Order::STATUS_CANCELLED => [
                'Pesanan Dibatalkan',
                $validated['admin_notes'] ?: 'Pesanan dibatalkan oleh Admin.',
            ],
        ];

        if (isset($historyMap[$newStatus])) {
            $order->logHistory($newStatus, $historyMap[$newStatus][0], $historyMap[$newStatus][1], $adminId);
        }

        return back()->with('success', 'Status pesanan ' . $order->order_number . ' berhasil diperbarui menjadi: ' . $order->status_label . ($updates['tracking_number'] ? ' (Resi: ' . $updates['tracking_number'] . ')' : ''));
    }

    /**
     * Tambah atau update Zona Tarif Ongkir J&T (Sesama Lamongan, Sekitar Lamongan, Jatim, Jawa, Luar Jawa, dll).
     */
    public function saveShippingZone(Request $request, ?ShippingZone $zone = null)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'courier' => ['required', 'string', 'max:60'],
            'service' => ['required', 'string', 'max:60'],
            'match_cities' => ['nullable', 'string', 'max:1000'],
            'match_provinces' => ['nullable', 'string', 'max:1000'],
            'cost_first_kg' => ['required', 'integer', 'min:0'],
            'cost_next_kg' => ['required', 'integer', 'min:0'],
            'etd' => ['required', 'string', 'max:40'],
            'free_shipping_min' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_default'] = $request->boolean('is_default', false);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 10);

        if ($validated['is_default']) {
            ShippingZone::query()->update(['is_default' => false]);
        }

        if ($zone && $zone->exists) {
            $zone->update($validated);
            $msg = 'Zona ongkir "' . $zone->name . '" berhasil diperbarui!';
        } else {
            ShippingZone::create($validated);
            $msg = 'Zona ongkir baru "' . $validated['name'] . '" berhasil ditambahkan!';
        }

        return redirect()->route('admin.orders.index', ['tab' => 'shipping'])->with('success', $msg);
    }

    public function deleteShippingZone(ShippingZone $zone)
    {
        $name = $zone->name;
        $zone->delete();

        return redirect()->route('admin.orders.index', ['tab' => 'shipping'])
            ->with('success', 'Zona ongkir "' . $name . '" telah dihapus.');
    }

    /**
     * Simpan pengaturan Payment Gateway Otomatis (Midtrans / Xendit / Simulator Otomatis).
     */
    public function updateGatewaySettings(Request $request)
    {
        $validated = $request->validate([
            'provider' => ['required', 'in:midtrans,xendit,builtin'],
            'mode' => ['required', 'in:sandbox,production'],
            'midtrans_client_key' => ['nullable', 'string', 'max:200'],
            'midtrans_server_key' => ['nullable', 'string', 'max:200'],
            'xendit_secret_key' => ['nullable', 'string', 'max:200'],
            'origin_city' => ['required', 'string', 'max:120'],
            'enable_auto_simulator' => ['nullable', 'boolean'],
        ]);

        $validated['enable_auto_simulator'] = $request->boolean('enable_auto_simulator');

        PageContent::saveContent(ShippingRateService::GATEWAY_KEY, $validated);

        return redirect()->route('admin.orders.index', ['tab' => 'gateway'])
            ->with('success', 'Pengaturan Payment Gateway Otomatis & Gudang Pengiriman berhasil disimpan!');
    }
}

