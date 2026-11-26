<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'rasa_kopi_cart';
    private const COUPON_KEY = 'rasa_kopi_coupon';

    /**
     * Get all items in the cart with fresh product instances.
     *
     * @return Collection<int, array{product: Product, quantity: int, subtotal: float}>
     */
    public function getItems(): Collection
    {
        $rawCart = Session::get(self::SESSION_KEY, []);
        $items = collect();

        if (empty($rawCart)) {
            return $items;
        }

        $productIds = array_keys($rawCart);
        $products = Product::whereIn('id', $productIds)->with('category')->get()->keyBy('id');

        foreach ($rawCart as $productId => $quantity) {
            if ($products->has($productId)) {
                $product = $products->get($productId);
                $qty = min((int) $quantity, (int) $product->stock);

                if ($qty > 0) {
                    $items->put($productId, [
                        'product' => $product,
                        'quantity' => $qty,
                        'subtotal' => (float) $product->price * $qty,
                    ]);
                }
            }
        }

        return $items;
    }

    /**
     * Add product to cart.
     */
    public function add(int $productId, int $quantity = 1): array
    {
        $product = Product::find($productId);

        if (! $product) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan.'];
        }

        if ($product->stock <= 0) {
            return ['success' => false, 'message' => 'Maaf, stok produk ini sedang habis.'];
        }

        $cart = Session::get(self::SESSION_KEY, []);
        $currentQty = $cart[$productId] ?? 0;
        $newQty = $currentQty + $quantity;

        if ($newQty > $product->stock) {
            return [
                'success' => false,
                'message' => "Stok tidak mencukupi. Maksimal pembelian adalah {$product->stock} item.",
            ];
        }

        $cart[$productId] = $newQty;
        Session::put(self::SESSION_KEY, $cart);

        return [
            'success' => true,
            'message' => "{$product->name} berhasil ditambahkan ke keranjang!",
            'total_items' => $this->getItemCount(),
        ];
    }

    /**
     * Update product quantity in cart.
     */
    public function update(int $productId, int $quantity): array
    {
        $product = Product::find($productId);

        if (! $product) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan.'];
        }

        $cart = Session::get(self::SESSION_KEY, []);

        if ($quantity <= 0) {
            unset($cart[$productId]);
            Session::put(self::SESSION_KEY, $cart);
            return ['success' => true, 'message' => 'Produk dihapus dari keranjang.'];
        }

        if ($quantity > $product->stock) {
            return [
                'success' => false,
                'message' => "Stok hanya tersedia {$product->stock} item.",
            ];
        }

        $cart[$productId] = $quantity;
        Session::put(self::SESSION_KEY, $cart);

        return ['success' => true, 'message' => 'Keranjang berhasil diperbarui.'];
    }

    /**
     * Remove product from cart.
     */
    public function remove(int $productId): array
    {
        $cart = Session::get(self::SESSION_KEY, []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put(self::SESSION_KEY, $cart);
        }

        return ['success' => true, 'message' => 'Produk telah dihapus dari keranjang.'];
    }

    /**
     * Empty cart completely.
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
        Session::forget(self::COUPON_KEY);
    }

    /**
     * Total number of items in cart.
     */
    public function getItemCount(): int
    {
        $cart = Session::get(self::SESSION_KEY, []);
        return (int) array_sum($cart);
    }

    /**
     * Calculate Subtotal.
     */
    public function getSubtotal(): float
    {
        $items = $this->getItems();
        return (float) $items->sum('subtotal');
    }

    /**
     * Apply promo coupon.
     */
    public function applyCoupon(string $code): array
    {
        $subtotal = $this->getSubtotal();
        if ($subtotal <= 0) {
            return ['success' => false, 'message' => 'Keranjang masih kosong.'];
        }

        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        if (! $coupon) {
            return ['success' => false, 'message' => 'Kode kupon tidak valid atau tidak ditemukan.'];
        }

        $check = $coupon->isValid($subtotal);
        if (! $check['valid']) {
            return ['success' => false, 'message' => $check['message']];
        }

        Session::put(self::COUPON_KEY, $coupon->code);

        $discount = $coupon->calculateDiscount($subtotal);
        $formattedDiscount = 'Rp ' . number_format($discount, 0, ',', '.');

        return [
            'success' => true,
            'message' => "Kupon {$coupon->code} berhasil diterapkan! Hemat {$formattedDiscount}.",
            'discount' => $discount,
        ];
    }

    /**
     * Remove applied coupon.
     */
    public function removeCoupon(): void
    {
        Session::forget(self::COUPON_KEY);
    }

    /**
     * Get applied coupon model.
     */
    public function getAppliedCoupon(): ?Coupon
    {
        $code = Session::get(self::COUPON_KEY);
        if (! $code) {
            return null;
        }

        $coupon = Coupon::where('code', $code)->first();
        if (! $coupon) {
            Session::forget(self::COUPON_KEY);
            return null;
        }

        $check = $coupon->isValid($this->getSubtotal());
        if (! $check['valid']) {
            Session::forget(self::COUPON_KEY);
            return null;
        }

        return $coupon;
    }

    /**
     * Calculate discount amount.
     */
    public function getDiscount(): float
    {
        $coupon = $this->getAppliedCoupon();
        if (! $coupon) {
            return 0.0;
        }

        return (float) $coupon->calculateDiscount($this->getSubtotal());
    }

    /**
     * Calculate shipping cost based on shipping type.
     */
    public function getShippingCost(string $shippingType = 'pickup'): float
    {
        if ($shippingType === 'delivery') {
            $flatRate = StoreSetting::get('shipping_flat_rate', '10000');
            return (float) $flatRate;
        }

        return 0.0;
    }

    /**
     * Calculate Grand Total.
     */
    public function getTotal(string $shippingType = 'pickup'): float
    {
        $subtotal = $this->getSubtotal();
        $discount = $this->getDiscount();
        $shipping = $this->getShippingCost($shippingType);

        $total = ($subtotal - $discount) + $shipping;
        return max(0.0, (float) $total);
    }
}
