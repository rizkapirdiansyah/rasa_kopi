<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = Review::with(['product', 'user', 'order'])->latest();

        if ($request->filled('status')) {
            if ($request->input('status') === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->input('status') === 'hidden') {
                $query->where('is_approved', false);
            }
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->input('rating'));
        }

        $reviews = $query->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleApproval(Review $review): RedirectResponse
    {
        $review->is_approved = ! $review->is_approved;
        $review->save();

        $status = $review->is_approved ? 'ditampilkan' : 'disembunyikan';
        return back()->with('success', "Ulasan berhasil {$status}.");
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();
        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
