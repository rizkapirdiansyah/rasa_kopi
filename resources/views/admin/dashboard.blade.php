@extends('layouts.admin')

@section('page_title', 'Ringkasan Bisnis & Dashboard')

@section('content')

<!-- KPI Metric Cards -->
<div class="row g-3 mb-4">
    <!-- Total Revenue -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Total Pendapatan</span>
                <span class="rounded-circle p-2 bg-success bg-opacity-10 text-success fs-5">
                    <i class="bi bi-cash-stack"></i>
                </span>
            </div>
            <h3 class="fw-bold text-dark font-serif mb-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <small class="text-muted"><i class="bi bi-check-circle-fill text-success me-1"></i> Dari transaksi terbayar/selesai</small>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Total Transaksi</span>
                <span class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary fs-5">
                    <i class="bi bi-bag-check"></i>
                </span>
            </div>
            <h3 class="fw-bold text-dark font-serif mb-1">{{ number_format($totalOrders) }}</h3>
            <small class="text-muted">Total pesanan masuk</small>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Total Produk Menu</span>
                <span class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning fs-5">
                    <i class="bi bi-cup-hot"></i>
                </span>
            </div>
            <h3 class="fw-bold text-dark font-serif mb-1">{{ number_format($totalProducts) }}</h3>
            <small class="text-muted">Item menu aktif di katalog</small>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Pelanggan Terdaftar</span>
                <span class="rounded-circle p-2 bg-info bg-opacity-10 text-info fs-5">
                    <i class="bi bi-people"></i>
                </span>
            </div>
            <h3 class="fw-bold text-dark font-serif mb-1">{{ number_format($totalCustomers) }}</h3>
            <small class="text-muted">Akun pelanggan aktif</small>
        </div>
    </div>
</div>

<!-- Sales Charts Section -->
<div class="row g-4 mb-4">
    <!-- Daily Sales Chart -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-graph-up me-2 text-caramel"></i> Tren Penjualan 7 Hari Terakhir</h6>
                <span class="badge badge-cream text-dark">Harian</span>
            </div>
            <div style="height: 260px;">
                <canvas id="dailySalesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Monthly Sales Chart -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-bar-chart me-2 text-caramel"></i> Penjualan 6 Bulan Terakhir</h6>
                <span class="badge badge-cream text-dark">Bulanan</span>
            </div>
            <div style="height: 260px;">
                <canvas id="monthlySalesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Best Sellers & Low Stock Alerts -->
<div class="row g-4 mb-4">
    <!-- Best Sellers -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-trophy text-warning me-2"></i> 5 Produk Menu Terlaris</h6>
                <a href="{{ route('admin.products.index') }}" class="small text-caramel text-decoration-none">Semua Produk &rarr;</a>
            </div>

            @if($bestSellers->isNotEmpty() && $bestSellers->first()->total_sold > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="small text-muted text-uppercase">
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th class="text-center">Terjual</th>
                                <th class="text-end">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bestSellers as $best)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $best->name }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-cream text-dark">{{ $best->category->name ?? '-' }}</span>
                                    </td>
                                    <td class="text-center fw-bold text-success">
                                        {{ $best->total_sold ?? 0 }} cup
                                    </td>
                                    <td class="text-end fw-semibold text-muted">
                                        {{ $best->formatted_price }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted small">
                    <i class="bi bi-info-circle d-block mb-1 fs-4 text-caramel opacity-50"></i>
                    Belum ada data penjualan tercatat. Penjualan produk terlaris akan otomatis muncul setelah transaksi diselesaikan.
                </div>
            @endif
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i> Peringatan Stok Menipis
                    @if($lowStockCount > 0)
                        <span class="badge bg-danger ms-1">{{ $lowStockCount }} Produk</span>
                    @endif
                </h6>
                <a href="{{ route('admin.stock.index') }}" class="small text-caramel text-decoration-none">Kelola Stok &rarr;</a>
            </div>

            @if($lowStockProducts->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="small text-muted text-uppercase">
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th class="text-center">Sisa Stok</th>
                                <th class="text-end">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $low)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $low->name }}</td>
                                    <td><span class="badge badge-cream text-dark">{{ $low->category->name ?? '-' }}</span></td>
                                    <td class="text-center">
                                        <span class="badge {{ $low->stock <= 0 ? 'bg-danger' : 'bg-warning text-dark' }} px-2 py-1">
                                            {{ $low->stock }} unit
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.stock.index', ['q' => $low->name]) }}" class="btn btn-outline-caramel btn-sm py-0 px-2">
                                            Tambah Stok
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-success small">
                    <i class="bi bi-shield-check d-block mb-1 fs-3"></i>
                    Seluruh stok produk kopi saat ini berada dalam kondisi aman (> 10 unit).
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-caramel"></i> Pesanan Terbaru</h6>
        <a href="{{ route('admin.orders.index') }}" class="small text-caramel text-decoration-none">Lihat Semua Pesanan &rarr;</a>
    </div>

    @if($recentOrders->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="small text-muted text-uppercase table-light">
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold font-monospace text-caramel text-decoration-none">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-bold text-dark small">{{ $order->customer_name }}</div>
                                <small class="text-muted">{{ $order->customer_phone }}</small>
                            </td>
                            <td class="small text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="small">
                                @if($order->shipping_type === 'pickup')
                                    <span class="badge bg-light text-dark border">Ambil di Toko</span>
                                @else
                                    <span class="badge bg-light text-dark border">Kurir</span>
                                @endif
                            </td>
                            <td class="fw-bold text-dark small">{{ $order->formatted_total }}</td>
                            <td>
                                <span class="badge {{ $order->status_badge_class }} small">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-outline-secondary btn-sm py-0 px-2">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-4 text-muted small">Belum ada pesanan terbaru.</div>
    @endif
</div>

@push('scripts')
<script>
    // 1. Daily Sales Chart
    const dailyCtx = document.getElementById('dailySalesChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyLabels) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode($dailyData) !!},
                borderColor: '#c67c4e',
                backgroundColor: 'rgba(198, 124, 78, 0.15)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#c67c4e',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // 2. Monthly Sales Chart
    const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyLabels) !!},
            datasets: [{
                label: 'Total Omzet (Rp)',
                data: {!! json_encode($monthlyData) !!},
                backgroundColor: '#3e2723',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>
@endpush

@endsection
