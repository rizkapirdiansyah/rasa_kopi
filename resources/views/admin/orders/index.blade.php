@extends('layouts.admin')

@section('page_title', 'Manajemen Pesanan')

@section('content')

<!-- Status Filter Tabs -->
<div class="d-flex gap-2 overflow-auto pb-2 mb-3">
    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-caramel' : 'btn-outline-secondary' }}">
        Semua ({{ $statusCounts['all'] }})
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'menunggu_pembayaran']) }}" class="btn btn-sm {{ request('status') === 'menunggu_pembayaran' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary' }}">
        Menunggu Bayar ({{ $statusCounts['menunggu_pembayaran'] }})
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'dibayar']) }}" class="btn btn-sm {{ request('status') === 'dibayar' ? 'btn-info text-dark fw-bold' : 'btn-outline-secondary' }}">
        Sudah Bayar / Verifikasi ({{ $statusCounts['dibayar'] }})
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'diproses']) }}" class="btn btn-sm {{ request('status') === 'diproses' ? 'btn-primary text-white fw-bold' : 'btn-outline-secondary' }}">
        Diproses Barista ({{ $statusCounts['diproses'] }})
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'siap_diambil']) }}" class="btn btn-sm {{ request('status') === 'siap_diambil' ? 'btn-purple text-white fw-bold' : 'btn-outline-secondary' }}">
        Siap Diambil ({{ $statusCounts['siap_diambil'] }})
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'dikirim']) }}" class="btn btn-sm {{ request('status') === 'dikirim' ? 'btn-info text-white fw-bold' : 'btn-outline-secondary' }}">
        Dikirim ({{ $statusCounts['dikirim'] }})
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'selesai']) }}" class="btn btn-sm {{ request('status') === 'selesai' ? 'btn-success text-white fw-bold' : 'btn-outline-secondary' }}">
        Selesai ({{ $statusCounts['selesai'] }})
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'dibatalkan']) }}" class="btn btn-sm {{ request('status') === 'dibatalkan' ? 'btn-danger text-white fw-bold' : 'btn-outline-secondary' }}">
        Dibatalkan ({{ $statusCounts['dibatalkan'] }})
    </a>
</div>

<!-- Search & Shipping Filter -->
<div class="card border-0 shadow-sm rounded-3 p-3 bg-white mb-4">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-2 align-items-center">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <div class="col-md-6">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nomor pesanan, nama pembeli, email, HP...">
            </div>
        </div>

        <div class="col-md-4">
            <select name="shipping_type" class="form-select form-select-sm">
                <option value="">Semua Metode Pengiriman</option>
                <option value="pickup" {{ request('shipping_type') === 'pickup' ? 'selected' : '' }}>Ambil di Toko (Pick-up)</option>
                <option value="delivery" {{ request('shipping_type') === 'delivery' ? 'selected' : '' }}>Pengiriman Kurir</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-caramel btn-sm w-100"><i class="bi bi-funnel"></i> Filter</button>
            @if(request()->hasAny(['q', 'shipping_type', 'status']))
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Orders Table -->
<div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th scope="col" class="ps-4">No. Pesanan</th>
                    <th scope="col">Pelanggan</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Pengiriman</th>
                    <th scope="col">Total</th>
                    <th scope="col">Metode Bayar</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-end pe-4">Aksi</th>
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

                        <td>
                            <div class="fw-bold text-dark small">{{ $order->customer_name }}</div>
                            <small class="text-muted">{{ $order->customer_phone }}</small>
                        </td>

                        <td class="small text-muted">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>

                        <td class="small">
                            @if($order->shipping_type === 'pickup')
                                <span class="badge bg-light text-dark border">Ambil di Toko</span>
                            @else
                                <span class="badge bg-light text-dark border">Kurir ({{ $order->shipping_city }})</span>
                            @endif
                        </td>

                        <td class="fw-bold text-dark small">
                            {{ $order->formatted_total }}
                        </td>

                        <td class="small text-muted">
                            {{ $order->payment_method_name }}
                        </td>

                        <td>
                            <span class="badge {{ $order->status_badge_class }} small">
                                {{ $order->status_label }}
                            </span>
                        </td>

                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm p-1 px-2" title="Kelola Pesanan">
                                    <i class="bi bi-gear"></i>
                                </a>
                                <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-outline-dark btn-sm p-1 px-2" title="Invoice">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            Tidak ada data pesanan yang sesuai dengan filter.
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
