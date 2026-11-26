<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category', 'approvedReviews');

        // Search
        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category')) {
            $categorySlug = (string) $request->input('category');
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Stock Filter
        if ($request->filled('stock_status')) {
            $stockStatus = (string) $request->input('stock_status');
            if ($stockStatus === 'in_stock') {
                $query->where('stock', '>', 0);
            } elseif ($stockStatus === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            }
        }

        // Sorting
        $sort = (string) $request->input('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->orderBy('id', 'desc'),
        };

        $products = $query->paginate(9)->withQueryString();
        $categories = Category::withCount('products')->get();

        return view('pages.catalog.index', compact('products', 'categories', 'sort'));
    }

    public function show(string $slug): View
    {
        $product = Product::with(['category', 'approvedReviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::with('category', 'approvedReviews')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        $userCanReview = false;
        if (auth()->check()) {
            $userCanReview = auth()->user()->orders()
                ->where('status', 'selesai')
                ->whereHas('items', function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })
                ->exists();
        }

        return view('pages.catalog.show', compact('product', 'relatedProducts', 'userCanReview'));
    }
}
