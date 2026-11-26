@extends('layouts.admin')

@section('page_title', 'Laporan Penjualan & Keuangan')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <p class="text-muted small mb-0">Analisis kinerja omzet dan ekspor laporan transaksi penjualan dalam format CSV (Excel).</p>
    </div>
    <a href="{{ route('admin.reports.export', ['start_date' => $startDate, 'end_date' => $endDate, 'status' => $status]) }}" class="btn btn-success btn-sm d-flex align-items-center gap-1 shadow-sm">
        <i class="bi bi-file-earmark-excel"></i> Unduh / Export CSV
    </a>
</div>

<!-- Date Range & Filter Card -->
<div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
    <form action="{{ route('admin.reports.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-bold">Dari Tanggal:</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm">
        </div>

        <div class="col-md-3">
            <label class="form-label small fw-bold">Sampai Tanggal:</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm">
        </div>

        <div class="col-md-3">
            <label class="form-label small fw-bold">Status Pesanan:</label>
            <select name="status" class="form-select form-select-sm">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="dibayar" {{ $status === 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                <option value="diproses" {{ $status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                <option value="siap_diambil" {{ $status === 'siap_diambil' ? 'selected' : '' }}>Siap Diambil</option>
                <option value="dikirim" {{ $status === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="dibatalkan" {{ $status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-caramel btn-sm w-100"><i class="bi bi-funnel"></i> Terapkan Filter</button>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-clockwise"></i></a>
        </div>
    </form>
</div>

<!-- Metrics Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center">
            <span class="text-muted small fw-bold text-uppercase">Total Omzet Penjualan</span>
            <h3 class="fw-bold text-success font-serif mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <small class="text-muted">Periode {{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center">
            <span class="text-muted small fw-bold text-uppercase">Jumlah Transaksi</span>
            <h3 class="fw-bold text-dark font-serif mb-0">{{ number_format($totalTransactions) }}</h3>
            <small class="text-muted">Total pesanan terfilter</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center">
            <span class="text-muted small fw-bold text-uppercase">Rata-rata Nilai Transaksi</span>
            <h3 class="fw-bold text-caramel font-serif mb-0">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</h3>
            <small class="text-muted">Average Order Value (AOV)</small>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th scope="col" class="ps-4">No. Pesanan</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Pelanggan</th>
                    <th scope="col">Subtotal</th>
                    <th scope="col">Diskon</th>
                    <th scope="col">Ongkir</th>
                    <th scope="col">Total</th>
                    <th scope="col">Metode</th>
                    <th scope="col" class="text-end pe-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="ps-4 py-3">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold font-monospace text-caramel text-decoration-none">
                                {{ $order->order_number }}
                            </a>
                        </td>

                        <td class="small text-muted">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            <div class="fw-bold text-dark small">{{ $order->customer_name }}</div>
                            <small class="text-muted">{{ $order->shipping_type === 'pickup' ? 'Ambil di Toko' : 'Kurir' }}</small>
                        </td>

                        <td class="small text-muted">{{ $order->formatted_subtotal }}</td>
                        <td class="small text-success">{{ $order->discount_amount > 0 ? '-' . $order->formatted_discount : '-' }}</td>
                        <td class="small text-muted">{{ $order->formatted_shipping_cost }}</td>

                        <td class="fw-bold text-dark small">
                            {{ $order->formatted_total }}
                        </td>

                        <td class="small text-muted">
                            {{ $order->payment_method_name }}
                        </td>

                        <td class="text-end pe-4">
                            <span class="badge {{ $order->status_badge_class }} small">
                                {{ $order->status_label }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            Tidak ada transaksi pada rentang tanggal ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 border-top d-flex justify-content-center">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
