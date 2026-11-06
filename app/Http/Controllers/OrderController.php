<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PageContent;
use App\Support\ShippingRateService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Daftar Pesanan Saya (gaya Shopee: Semua, Belum Bayar, Dikemas, Dikirim, Selesai, Dibatalkan).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $tab = $request->query('status', 'all');

        $allOrders = Order::with(['items.product', 'shippingZone'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $counts = [
            'all' => $allOrders->count(),
            Order::STATUS_PENDING_PAYMENT => $allOrders->where('status', Order::STATUS_PENDING_PAYMENT)->count(),
            'processing_group' => $allOrders->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING])->count(),
            Order::STATUS_SHIPPED => $allOrders->whereIn('status', [Order::STATUS_SHIPPED, Order::STATUS_DELIVERED])->count(),
            Order::STATUS_COMPLETED => $allOrders->where('status', Order::STATUS_COMPLETED)->count(),
            Order::STATUS_CANCELLED => $allOrders->where('status', Order::STATUS_CANCELLED)->count(),
        ];

        $orders = match ($tab) {
            Order::STATUS_PENDING_PAYMENT => $allOrders->where('status', Order::STATUS_PENDING_PAYMENT)->values(),
            'processing_group', Order::STATUS_PAID, Order::STATUS_PROCESSING => $allOrders->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING])->values(),
            Order::STATUS_SHIPPED => $allOrders->whereIn('status', [Order::STATUS_SHIPPED, Order::STATUS_DELIVERED])->values(),
            Order::STATUS_COMPLETED => $allOrders->where('status', Order::STATUS_COMPLETED)->values(),
            Order::STATUS_CANCELLED => $allOrders->where('status', Order::STATUS_CANCELLED)->values(),
            default => $allOrders,
        };

        return view('shop.orders.index', compact('orders', 'counts', 'tab'));
    }

    /**
     * Detail Pesanan + Payment Gateway Otomatis + Tracking Pengiriman J&T Express.
     */
    public function show(Request $request, Order $order)
    {
        abort_if($order->user_id !== $request->user()->id && !$request->user()->isAdmin(), 403);

        $order->load(['items.product', 'statusHistories.creator', 'shippingZone', 'affiliate']);

        $purchaseContent = AdminPurchasePageController::content();
        $qrisImg = PageContent::imageUrl($purchaseContent['general']['qris_image'] ?? 'images/qris-urskynd.jpg');
        $qrisMerchant = $purchaseContent['general']['qris_merchant'] ?? 'URSKYND SKINCARE';
        $qrisNmid = $purchaseContent['general']['qris_nmid'] ?? 'ID1026569298288';
        $gatewaySettings = ShippingRateService::gatewaySettings();

        return view('shop.orders.show', compact(
            'order',
            'qrisImg',
            'qrisMerchant',
            'qrisNmid',
            'gatewaySettings'
        ));
    }

    /**
     * Pembayaran Otomatis via Payment Gateway (Midtrans / Xendit / Auto-Verify Simulator) + Upload Bukti.
     */
    public function pay(Request $request, Order $order)
    {
        abort_if($order->user_id !== $request->user()->id && !$request->user()->isAdmin(), 403);

        if ($order->payment_status === 'paid') {
            return back()->with('success', 'Pesanan ini sudah berstatus Lunas (Pembayaran Diterima).');
        }

        $validated = $request->validate([
            'payment_method' => ['nullable', 'string', 'max:60'],
            'payment_proof_file' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('payment_proof_file')) {
            $path = $request->file('payment_proof_file')->store('payment_proofs', 'public');
            $order->update(['payment_proof' => $path]);
        }

        $method = $validated['payment_method'] ?? $order->payment_method ?: 'QRIS / Virtual Account (Payment Gateway)';
        $ref = 'PG-' . now()->format('ymdHis') . '-' . strtoupper(substr(uniqid(), -4));

        $order->markAsPaid($method, $ref, $request->user()->id);

        return redirect()->route('orders.show', $order)
            ->with('success', '✅ Pembayaran Otomatis Berhasil Diterima! Status pesanan Anda kini: Pembayaran Diterima & Sedang Disiapkan Gudang Lamongan.');
    }

    /**
     * Webhook callback untuk Midtrans / Xendit Payment Gateway otomatis.
     */
    public function paymentWebhook(Request $request)
    {
        $orderNumber = $request->input('order_id') ?? $request->input('external_id');
        if (!$orderNumber) {
            return response()->json(['message' => 'Missing order_id'], 400);
        }

        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $txStatus = strtolower((string) ($request->input('transaction_status') ?? $request->input('status') ?? ''));
        $paymentType = (string) ($request->input('payment_type') ?? $request->input('payment_channel') ?? 'Payment Gateway');
        $txId = (string) ($request->input('transaction_id') ?? $request->input('id') ?? '');

        if (in_array($txStatus, ['settlement', 'capture', 'paid', 'succeeded'], true)) {
            $order->markAsPaid(strtoupper($paymentType), $txId);
        } elseif (in_array($txStatus, ['expire', 'cancel', 'deny', 'failed'], true)) {
            $order->update([
                'status' => Order::STATUS_CANCELLED,
                'payment_status' => 'expired',
                'cancelled_at' => now(),
                'cancel_reason' => 'Pembayaran kedaluwarsa atau dibatalkan dari Payment Gateway.',
            ]);
            $order->logHistory(Order::STATUS_CANCELLED, 'Pembayaran Kedaluwarsa / Dibatalkan', 'Status dari Payment Gateway: ' . $txStatus);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Pembeli mengonfirmasi "Pesanan Diterima" (seperti tombol Selesai di Shopee).
     */
    public function confirmReceived(Request $request, Order $order)
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        if (!in_array($order->status, [Order::STATUS_SHIPPED, Order::STATUS_DELIVERED], true)) {
            return back()->withErrors(['order' => 'Pesanan belum berada pada tahap pengiriman.']);
        }

        $order->update([
            'status' => Order::STATUS_COMPLETED,
            'delivered_at' => $order->delivered_at ?: now(),
            'completed_at' => now(),
        ]);

        $order->logHistory(
            Order::STATUS_COMPLETED,
            'Pesanan Diterima oleh Pembeli — Transaksi Selesai',
            'Pembeli telah mengonfirmasi paket diterima dalam kondisi baik. Terima kasih telah berbelanja di URSKYND Official Store!',
            $request->user()->id
        );

        return back()->with('success', '🎉 Terima kasih! Pesanan telah ditandai Selesai.');
    }

    /**
     * Batalkan pesanan jika masih Belum Bayar.
     */
    public function cancel(Request $request, Order $order)
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        if ($order->status !== Order::STATUS_PENDING_PAYMENT) {
            return back()->withErrors(['order' => 'Pesanan yang sudah dibayar tidak dapat dibatalkan sendiri.']);
        }

        $order->update([
            'status' => Order::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancel_reason' => $request->input('reason', 'Dibatalkan oleh pembeli.'),
        ]);

        $order->logHistory(
            Order::STATUS_CANCELLED,
            'Pesanan Dibatalkan oleh Pembeli',
            $order->cancel_reason,
            $request->user()->id
        );

        return back()->with('success', 'Pesanan telah dibatalkan.');
    }
}

