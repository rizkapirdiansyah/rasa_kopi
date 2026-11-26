<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $status = $request->input('status', 'all');

        $query = Order::with(['user', 'items.product', 'paymentMethod'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(20)->withQueryString();

        // Calculate metrics
        $paidStatuses = ['dibayar', 'diproses', 'siap_diambil', 'dikirim', 'selesai'];
        $baseQuery = Order::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($status !== 'all') {
            $baseQuery->where('status', $status);
        }

        $totalTransactions = $baseQuery->count();
        $totalRevenue = (float) (clone $baseQuery)->whereIn('status', $paidStatuses)->sum('total_amount');
        $averageOrderValue = $totalTransactions > 0 ? $totalRevenue / max(1, (clone $baseQuery)->whereIn('status', $paidStatuses)->count()) : 0;

        return view('admin.reports.index', compact(
            'orders',
            'startDate',
            'endDate',
            'status',
            'totalTransactions',
            'totalRevenue',
            'averageOrderValue'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $status = $request->input('status', 'all');

        $query = Order::with(['user', 'items.product', 'paymentMethod'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('id', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $filename = "Laporan_Penjualan_Rasa_Kopi_{$startDate}_sampai_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Header
            fputcsv($handle, [
                'No. Pesanan',
                'Tanggal',
                'Nama Pelanggan',
                'Email',
                'No. Telepon',
                'Metode Pengiriman',
                'Kota',
                'Metode Pembayaran',
                'Subtotal (Rp)',
                'Diskon (Rp)',
                'Ongkir (Rp)',
                'Total (Rp)',
                'Status Pesanan',
                'Rincian Produk',
            ]);

            $query->chunk(100, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    $itemDescriptions = $order->items->map(function ($item) {
                        return "{$item->product_name} (x{$item->quantity})";
                    })->implode('; ');

                    fputcsv($handle, [
                        $order->order_number,
                        $order->created_at->format('d/m/Y H:i'),
                        $order->customer_name,
                        $order->customer_email,
                        $order->customer_phone,
                        $order->shipping_type === 'pickup' ? 'Ambil di Toko' : 'Pengiriman Kurir',
                        $order->shipping_city ?? '-',
                        $order->payment_method_name,
                        (float) $order->subtotal,
                        (float) $order->discount_amount,
                        (float) $order->shipping_cost,
                        (float) $order->total_amount,
                        $order->status_label,
                        $itemDescriptions,
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
