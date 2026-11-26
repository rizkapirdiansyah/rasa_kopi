@extends('layouts.admin')

@section('page_title', 'Detail Pelanggan: ' . $customer->name)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pelanggan
    </a>
</div>

<div class="row g-4 mb-4">
    <!-- Customer Profile Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
            <div class="text-center mb-3">
                <div class="bg-caramel text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold fs-2 mb-2" style="width: 72px; height: 72px;">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold text-dark mb-0 font-serif">{{ $customer->name }}</h5>
                <small class="text-muted">{{ $customer->email }}</small>
            </div>

            <hr>

            <div class="mb-2 small">
                <span class="text-muted d-block">WhatsApp / HP:</span>
                <strong>{{ $customer->phone ?? '-' }}</strong>
            </div>

            <div class="mb-2 small">
                <span class="text-muted d-block">Terdaftar Sejak:</span>
                <strong>{{ $customer->created_at->format('d F Y, H:i') }}</strong>
            </div>

            <div class="mb-2 small">
                <span class="text-muted d-block">Total Belanja:</span>
                <strong class="text-caramel fs-5">Rp {{ number_format($totalSpent, 0, ',', '.') }}</strong>
            </div>

            <div class="mt-3">
                @if($customer->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" target="_blank" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-whatsapp me-1"></i> Hubungi Pelanggan via WA
                    </a>
                @endif
            </div>
        </div>

        <!-- Addresses -->
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
            <h6 class="fw-bold font-serif text-dark mb-3">Alamat Tersimpan ({{ $customer->addresses->count() }})</h6>
            @forelse($customer->addresses as $addr)
                <div class="p-2 border rounded mb-2 small bg-light">
                    <span class="badge bg-secondary me-1">{{ $addr->label }}</span>
                    <strong>{{ $addr->recipient_name }}</strong>
                    <div class="text-muted">{{ $addr->address_line }}, {{ $addr->city }}</div>
                </div>
            @empty
                <div class="text-muted small">Belum ada alamat tersimpan.</div>
            @endforelse
        </div>
    </div>

    <!-- Purchase History -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
            <div class="p-3 border-bottom bg-light">
                <h6 class="fw-bold mb-0">Riwayat Pesanan Pelanggan ({{ $customer->orders->count() }})</h6>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="small text-muted text-uppercase">
                        <tr>
                            <th class="ps-3">No. Pesanan</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->orders as $order)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold font-monospace text-caramel text-decoration-none">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="small text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="fw-bold small">{{ $order->formatted_total }}</td>
                                <td>
                                    <span class="badge {{ $order->status_badge_class }} small">{{ $order->status_label }}</span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm py-0 px-2">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">Pelanggan ini belum pernah melakukan pemesanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
