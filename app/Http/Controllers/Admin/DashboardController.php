<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. KPI Summaries
        $paidStatuses = ['dibayar', 'diproses', 'siap_diambil', 'dikirim', 'selesai'];

        $totalRevenue = (float) Order::whereIn('status', $paidStatuses)->sum('total_amount');
        $totalOrders = (int) Order::count();
        $totalProducts = (int) Product::count();
        $totalCustomers = (int) User::where('role', 'pelanggan')->count();

        // 2. Low Stock Alert
        $lowStockProducts = Product::with('category')
            ->where('stock', '<=', 10)
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();
        $lowStockCount = Product::where('stock', '<=', 10)->count();

        // 3. Top Selling Products
        $bestSellers = Product::with('category')
            ->withSum(['orderItems as total_sold' => function ($q) use ($paidStatuses) {
                $q->whereHas('order', function ($oq) use ($paidStatuses) {
                    $oq->whereIn('status', $paidStatuses);
                });
            }], 'quantity')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // 4. Recent Orders
        $recentOrders = Order::with('user', 'payment')
            ->latest()
            ->take(6)
            ->get();

        // 5. Chart Data: Daily Sales (Last 7 Days)
        $dailyLabels = [];
        $dailyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailyLabels[] = $date->format('d M');
            $dailyRevenue = (float) Order::whereIn('status', $paidStatuses)
                ->whereDate('created_at', $date)
                ->sum('total_amount');
            $dailyData[] = $dailyRevenue;
        }

        // 6. Chart Data: Monthly Sales (Last 6 Months)
        $monthlyLabels = [];
        $monthlyData = [];
        for ($m = 5; $m >= 0; $m--) {
            $month = Carbon::today()->subMonths($m);
            $monthlyLabels[] = $month->format('M Y');
            $monthRevenue = (float) Order::whereIn('status', $paidStatuses)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');
            $monthlyData[] = $monthRevenue;
        }

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'lowStockProducts',
            'lowStockCount',
            'bestSellers',
            'recentOrders',
            'dailyLabels',
            'dailyData',
            'monthlyLabels',
            'monthlyData'
        ));
    }
}
