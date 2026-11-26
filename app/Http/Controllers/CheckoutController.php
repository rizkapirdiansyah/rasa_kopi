<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Services\CartService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }

    public function index(): View|RedirectResponse
    {
        $items = $this->cartService->getItems();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $user = auth()->user();
        $subtotal = $this->cartService->getSubtotal();
        $coupon = $this->cartService->getAppliedCoupon();
        $discount = $this->cartService->getDiscount();
        $shippingFlatRate = $this->cartService->getShippingCost('delivery');
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $savedAddresses = $user ? $user->addresses()->latest()->get() : collect();

        return view('pages.checkout.index', compact(
            'items',
            'user',
            'subtotal',
            'coupon',
            'discount',
            'shippingFlatRate',
            'paymentMethods',
            'savedAddresses'
        ));
    }

    public function process(CheckoutRequest $request): RedirectResponse
    {
        $items = $this->cartService->getItems();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $user = auth()->user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk menyelesaikan pesanan Anda.');
        }

        $shippingType = (string) $request->input('shipping_type', 'pickup');
        $paymentMethod = PaymentMethod::findOrFail((int) $request->input('payment_method_id'));
        $coupon = $this->cartService->getAppliedCoupon();

        $subtotal = $this->cartService->getSubtotal();
        $discount = $this->cartService->getDiscount();
        $shippingCost = $this->cartService->getShippingCost($shippingType);
        $totalAmount = max(0, ($subtotal - $discount) + $shippingCost);

        // Generate unique order number (e.g. RSK-20261126-10492)
        $orderNumber = 'RSK-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        try {
            $order = DB::transaction(function () use (
                $request,
                $user,
                $items,
                $shippingType,
                $paymentMethod,
                $coupon,
                $subtotal,
                $discount,
                $shippingCost,
                $totalAmount,
                $orderNumber
            ) {
                // 1. Validate real-time stock and lock rows
                foreach ($items as $item) {
                    $product = Product::lockForUpdate()->find($item['product']->id);

                    if (! $product || $product->stock < $item['quantity']) {
                        throw new Exception("Stok untuk produk '{$item['product']->name}' tidak mencukupi (tersedia: " . ($product?->stock ?? 0) . ").");
                    }
                }

                // 2. Create Order
                $order = Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => $user->id,
                    'customer_name' => $request->input('customer_name'),
                    'customer_email' => $request->input('customer_email'),
                    'customer_phone' => $request->input('customer_phone'),
                    'shipping_type' => $shippingType,
                    'shipping_address' => $shippingType === 'delivery' ? $request->input('shipping_address') : null,
                    'shipping_city' => $shippingType === 'delivery' ? $request->input('shipping_city') : null,
                    'shipping_cost' => $shippingCost,
                    'pickup_time' => $shippingType === 'pickup' ? $request->input('pickup_time') : null,
                    'coupon_id' => $coupon?->id,
                    'coupon_code' => $coupon?->code,
                    'discount_amount' => $discount,
                    'subtotal' => $subtotal,
                    'total_amount' => $totalAmount,
                    'payment_method_id' => $paymentMethod->id,
                    'payment_method_name' => $paymentMethod->name,
                    'status' => $paymentMethod->code === 'cash' ? 'menunggu_pembayaran' : 'menunggu_pembayaran',
                    'customer_notes' => $request->input('customer_notes'),
                ]);

                // 3. Create Order Items & Decrement Stock
                foreach ($items as $item) {
                    $product = Product::lockForUpdate()->find($item['product']->id);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    // Decrement stock atomically
                    $product->decrement('stock', $item['quantity']);
                }

                // 4. Update Coupon Usage if used
                if ($coupon) {
                    $coupon->increment('used_count');
                }

                // 5. Create Initial Payment record
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $totalAmount,
                    'payment_method' => $paymentMethod->name,
                    'status' => 'pending',
                ]);

                return $order;
            });

            // Clear cart
            $this->cartService->clear();

            return redirect()->route('orders.confirmation', $order->order_number)
                ->with('success', 'Pesanan Anda berhasil dibuat!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage());
        }
    }
}
