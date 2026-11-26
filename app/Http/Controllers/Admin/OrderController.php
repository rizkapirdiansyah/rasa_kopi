<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StoreSetting;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'items.product', 'payment'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('shipping_type')) {
            $query->where('shipping_type', $request->input('shipping_type'));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($query) use ($q) {
                $query->where('order_number', 'like', "%{$q}%")
                      ->orWhere('customer_name', 'like', "%{$q}%")
                      ->orWhere('customer_phone', 'like', "%{$q}%")
                      ->orWhere('customer_email', 'like', "%{$q}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        $statusCounts = [
            'all' => Order::count(),
            'menunggu_pembayaran' => Order::where('status', 'menunggu_pembayaran')->count(),
            'dibayar' => Order::where('status', 'dibayar')->count(),
            'diproses' => Order::where('status', 'diproses')->count(),
            'siap_diambil' => Order::where('status', 'siap_diambil')->count(),
            'dikirim' => Order::where('status', 'dikirim')->count(),
            'selesai' => Order::where('status', 'selesai')->count(),
            'dibatalkan' => Order::where('status', 'dibatalkan')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.product.category', 'payment.verifiedByUser', 'paymentMethod']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,menunggu_pembayaran,dibayar,diproses,siap_diambil,dikirim,selesai,dibatalkan'],
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->input('status');

        try {
            DB::transaction(function () use ($order, $oldStatus, $newStatus, $request) {
                // If cancelled now, return stock
                if ($newStatus === 'dibatalkan' && $oldStatus !== 'dibatalkan') {
                    foreach ($order->items as $item) {
                        if ($item->product) {
                            $item->product->increment('stock', $item->quantity);
                        }
                    }
                }

                // If reactivated from cancelled, decrement stock if available
                if ($oldStatus === 'dibatalkan' && $newStatus !== 'dibatalkan') {
                    foreach ($order->items as $item) {
                        if ($item->product) {
                            if ($item->product->stock < $item->quantity) {
                                throw new Exception("Stok untuk {$item->product->name} tidak mencukupi untuk mengaktifkan kembali pesanan ini.");
                            }
                            $item->product->decrement('stock', $item->quantity);
                        }
                    }
                }

                $order->status = $newStatus;
                if ($request->filled('admin_notes')) {
                    $order->admin_notes = $request->input('admin_notes');
                }
                $order->save();
            });

            return back()->with('success', "Status pesanan #{$order->order_number} berhasil diubah menjadi: {$order->status_label}.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function verifyPayment(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string', 'max:500'],
        ]);

        $payment = $order->payment;
        if (! $payment) {
            return back()->with('error', 'Pesanan ini belum memiliki data pembayaran.');
        }

        if ($request->input('action') === 'approve') {
            $payment->update([
                'status' => 'verified',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
                'rejection_reason' => null,
            ]);

            // Automatically move order to 'diproses'
            $order->update(['status' => 'diproses']);

            return back()->with('success', 'Pembayaran berhasil diverifikasi! Pesanan kini berstatus Diproses.');
        }

        $payment->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        $order->update(['status' => 'menunggu_pembayaran']);

        return back()->with('warning', 'Bukti pembayaran ditolak. Pelanggan diminta mengunggah ulang bukti pembayaran.');
    }

    public function invoice(Order $order): View
    {
        $order->load(['user', 'items.product', 'payment', 'paymentMethod']);

        $storeSettings = [
            'name' => StoreSetting::get('store_name', 'RASA KOPI'),
            'address' => StoreSetting::get('store_address', 'Jl. Kopi Kenikmatan No. 18, Jakarta Selatan'),
            'phone' => StoreSetting::get('store_phone', '0812-3456-7890'),
            'email' => StoreSetting::get('store_email', 'halo@rasakopi.com'),
        ];

        return view('pages.orders.invoice', compact('order', 'storeSettings'));
    }
}
