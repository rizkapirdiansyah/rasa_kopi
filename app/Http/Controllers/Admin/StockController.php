<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category')->orderBy('stock', 'asc');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'out') {
                $query->where('stock', '<=', 0);
            } elseif ($request->input('status') === 'low') {
                $query->where('stock', '>', 0)->where('stock', '<=', 10);
            } elseif ($request->input('status') === 'safe') {
                $query->where('stock', '>', 10);
            }
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where('name', 'like', "%{$q}%");
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        $stats = [
            'total' => Product::count(),
            'out' => Product::where('stock', '<=', 0)->count(),
            'low' => Product::where('stock', '>', 0)->where('stock', '<=', 10)->count(),
            'safe' => Product::where('stock', '>', 10)->count(),
        ];

        return view('admin.stock.index', compact('products', 'categories', 'stats'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'action_type' => ['required', 'in:set,add'],
            'stock_value' => ['required', 'integer', 'min:0'],
        ]);

        $action = $request->input('action_type');
        $value = (int) $request->input('stock_value');

        if ($action === 'set') {
            $product->stock = $value;
        } else {
            $product->stock += $value;
        }

        $product->save();

        return back()->with('success', "Stok untuk {$product->name} berhasil diperbarui menjadi {$product->stock} unit.");
    }
}
