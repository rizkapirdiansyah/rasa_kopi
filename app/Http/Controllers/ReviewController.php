<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, int $productId): RedirectResponse
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ], [
            'rating.required' => 'Pilih bintang rating (1-5).',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'comment.required' => 'Tuliskan ulasan Anda tentang kopi ini.',
        ]);

        $user = auth()->user();
        $product = Product::findOrFail($productId);

        // Verify if user bought this product and order status is 'selesai'
        $completedOrder = $user->orders()
            ->where('status', 'selesai')
            ->whereHas('items', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->latest()
            ->first();

        if (! $completedOrder) {
            return back()->with('error', 'Anda hanya dapat memberikan ulasan pada produk yang telah selesai Anda beli.');
        }

        // Check if already reviewed for this order
        $existingReview = Review::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->where('order_id', $completedOrder->id)
            ->first();

        if ($existingReview) {
            $existingReview->update([
                'rating' => $request->input('rating'),
                'comment' => $request->input('comment'),
                'is_approved' => true,
            ]);
            return back()->with('success', 'Ulasan Anda berhasil diperbarui!');
        }

        Review::create([
            'user_id' => $user->id,
            'product_id' => $productId,
            'order_id' => $completedOrder->id,
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
            'is_approved' => true,
        ]);

        return back()->with('success', 'Terima kasih atas ulasan dan rating yang Anda berikan!');
    }
}
