<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }

    public function index(): View
    {
        $items = $this->cartService->getItems();
        $subtotal = $this->cartService->getSubtotal();
        $coupon = $this->cartService->getAppliedCoupon();
        $discount = $this->cartService->getDiscount();
        $total = $this->cartService->getTotal();

        return view('pages.cart.index', compact('items', 'subtotal', 'coupon', 'discount', 'total'));
    }

    public function add(Request $request, int $id): RedirectResponse
    {
        $quantity = max(1, (int) $request->input('quantity', 1));
        $result = $this->cartService->add($id, $quantity);

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        if ($request->input('action') === 'buy_now') {
            return redirect()->route('checkout.index');
        }

        return back()->with('success', $result['message']);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $quantity = (int) $request->input('quantity', 1);
        $result = $this->cartService->update($id, $quantity);

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    public function remove(int $id): RedirectResponse
    {
        $result = $this->cartService->remove($id);
        return back()->with('success', $result['message']);
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $request->validate([
            'coupon_code' => ['required', 'string'],
        ], [
            'coupon_code.required' => 'Masukkan kode kupon diskon.',
        ]);

        $result = $this->cartService->applyCoupon($request->input('coupon_code'));

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    public function removeCoupon(): RedirectResponse
    {
        $this->cartService->removeCoupon();
        return back()->with('success', 'Kupon diskon berhasil dihapus dari pesanan.');
    }
}
