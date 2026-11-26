<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\StoreSetting;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function confirmation(string $orderNumber): View
    {
        $order = Order::with(['items.product', 'payment', 'paymentMethod'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Customer can only view their own order unless admin
        if (! auth()->user()->isAdmin() && $order->user_id !== auth()->id()) {
            abort(403, 'Akses pesanan tidak diizinkan.');
        }

        $storeWhatsApp = StoreSetting::get('store_whatsapp', '6281234567890');
        $whatsappMessage = urlencode("Halo Rasa Kopi, saya ingin konfirmasi pesanan dengan nomor: {$order->order_number} atas nama {$order->customer_name} senilai {$order->formatted_total}.");

        return view('pages.orders.confirmation', compact('order', 'storeWhatsApp', 'whatsappMessage'));
    }

    public function index(): View
    {
        $orders = auth()->user()->orders()
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate(10);

        return view('pages.orders.index', compact('orders'));
    }

    public function show(string $orderNumber): View
    {
        $order = Order::with(['items.product', 'payment', 'paymentMethod'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if (! auth()->user()->isAdmin() && $order->user_id !== auth()->id()) {
            abort(403, 'Akses pesanan tidak diizinkan.');
        }

        $storeWhatsApp = StoreSetting::get('store_whatsapp', '6281234567890');
        $whatsappMessage = urlencode("Halo Rasa Kopi, saya ingin menanyakan status pesanan saya #{$order->order_number}.");

        return view('pages.orders.show', compact('order', 'storeWhatsApp', 'whatsappMessage'));
    }

    public function invoice(string $orderNumber): View
    {
        $order = Order::with(['items.product', 'payment', 'paymentMethod', 'user'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if (! auth()->user()->isAdmin() && $order->user_id !== auth()->id()) {
            abort(403, 'Akses pesanan tidak diizinkan.');
        }

        $storeSettings = [
            'name' => StoreSetting::get('store_name', 'RASA KOPI'),
            'address' => StoreSetting::get('store_address', 'Jl. Kopi Kenikmatan No. 18, Jakarta Selatan'),
            'phone' => StoreSetting::get('store_phone', '0812-3456-7890'),
            'email' => StoreSetting::get('store_email', 'halo@rasakopi.com'),
        ];

        return view('pages.orders.invoice', compact('order', 'storeSettings'));
    }

    public function uploadPaymentProof(Request $request, string $orderNumber): RedirectResponse
    {
        $request->validate([
            'proof_image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'sender_bank' => ['nullable', 'string', 'max:100'],
            'sender_name' => ['nullable', 'string', 'max:100'],
        ], [
            'proof_image.required' => 'File bukti transfer wajib diunggah.',
            'proof_image.image' => 'File harus berupa gambar.',
            'proof_image.mimes' => 'Format file yang diperbolehkan: JPEG, PNG, JPG, WEBP.',
            'proof_image.max' => 'Ukuran maksimal file adalah 2MB.',
        ]);

        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        if (! auth()->user()->isAdmin() && $order->user_id !== auth()->id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $path = $request->file('proof_image')->store('payment_proofs', 'public');

        $payment = $order->payment ?? new Payment(['order_id' => $order->id]);
        $payment->amount = $order->total_amount;
        $payment->payment_method = $order->payment_method_name;
        $payment->proof_image = $path;
        $payment->sender_bank = $request->input('sender_bank');
        $payment->sender_name = $request->input('sender_name');
        $payment->status = 'pending';
        $payment->save();

        // Update order status to 'dibayar' (meaning customer has paid / uploaded proof, waiting admin review)
        $order->status = 'dibayar';
        $order->save();

        return back()->with('success', 'Bukti pembayaran berhasil diunggah! Tim kami akan segera memverifikasinya.');
    }

    public function cancel(string $orderNumber): RedirectResponse
    {
        $order = Order::with('items.product')->where('order_number', $orderNumber)->firstOrFail();

        if (! auth()->user()->isAdmin() && $order->user_id !== auth()->id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if (! $order->canBeCancelled()) {
            return back()->with('error', 'Pesanan ini sudah diproses dan tidak dapat dibatalkan.');
        }

        try {
            DB::transaction(function () use ($order) {
                // Restore stock
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }

                $order->status = 'dibatalkan';
                $order->save();
            });

            return back()->with('success', 'Pesanan berhasil dibatalkan dan stok produk telah dikembalikan.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }
}
