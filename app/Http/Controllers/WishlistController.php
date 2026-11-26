<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }

    public function index(): View
    {
        $wishlists = auth()->user()->wishlists()
            ->with(['product.category'])
            ->latest()
            ->paginate(12);

        return view('pages.wishlist.index', compact('wishlists'));
    }

    public function toggle(int $productId): RedirectResponse
    {
        $user = auth()->user();
        $product = Product::findOrFail($productId);

        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($exists) {
            $exists->delete();
            return back()->with('success', "{$product->name} telah dihapus dari wishlist.");
        }

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        return back()->with('success', "{$product->name} berhasil ditambahkan ke wishlist!");
    }

    public function moveToCart(int $productId): RedirectResponse
    {
        $user = auth()->user();
        $wishlist = Wishlist::where('user_id', $user->id)->where('product_id', $productId)->first();

        if ($wishlist) {
            $result = $this->cartService->add($productId, 1);
            if ($result['success']) {
                $wishlist->delete();
                return back()->with('success', 'Produk berhasil dipindahkan ke keranjang belanja!');
            }
            return back()->with('error', $result['message']);
        }

        return back()->with('error', 'Produk tidak ditemukan di wishlist.');
    }
}
