<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'pelanggan')
            ->withCount('orders')
            ->withSum(['orders as total_spent' => function ($q) {
                $q->whereIn('status', ['dibayar', 'diproses', 'siap_diambil', 'dikirim', 'selesai']);
            }], 'total_amount')
            ->latest();

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer): View
    {
        if ($customer->role !== 'pelanggan') {
            abort(404);
        }

        $customer->load(['addresses', 'orders.items.product', 'reviews.product']);
        $totalSpent = (float) $customer->orders()
            ->whereIn('status', ['dibayar', 'diproses', 'siap_diambil', 'dikirim', 'selesai'])
            ->sum('total_amount');

        return view('admin.customers.show', compact('customer', 'totalSpent'));
    }
}
