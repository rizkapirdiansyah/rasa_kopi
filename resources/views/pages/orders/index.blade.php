@extends('layouts.app')

@section('title', 'Riwayat Pesanan Saya - RASA KOPI')

@section('content')

<div class="container py-4 mb-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold font-serif text-dark mb-0">Riwayat Pesanan Saya</h1>
            <p class="text-muted small mb-0">Pantau status pembuatan kopi dan pengiriman pesanan Anda secara real-time.</p>
        </div>
        <a href="{{ route('catalog.index') }}" class="btn btn-caramel btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Pesan Kopi Baru
        </a>
    </div>

    @if($orders->isNotEmpty())
        <div class="card card-coffee border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th scope="col" class="ps-4">No. Pesanan</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Menu Pesanan</th>
                            <th scope="col">Pengiriman</th>
                            <th scope="col">Total</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <!-- Order Number -->
                                <td class="ps-4 py-3">
                                    <a href="{{ route('orders.show', $order->order_number) }}" class="fw-bold font-monospace text-caramel text-decoration-none">
                                        {{ $order->order_number }}
                                    </a>
                                </td>

                                <!-- Date -->
                                <td class="text-muted small">
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </td>

                                <!-- Items -->
                                <td>
                                    <div class="small">
                                        @foreach($order->items->take(2) as $item)
                                            <div>{{ $item->product_name }} <span class="text-muted">(x{{ $item->quantity }})</span></div>
                                        @endforeach
                                        @if($order->items->count() > 2)
                                            <span class="text-muted small">+{{ $order->items->count() - 2 }} menu lainnya</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Shipping -->
                                <td class="small">
                                    @if($order->shipping_type === 'pickup')
                                        <span class="badge bg-light text-dark border"><i class="bi bi-shop text-caramel"></i> Ambil di Toko</span>
                                    @else
                                        <span class="badge bg-light text-dark border"><i class="bi bi-bicycle text-caramel"></i> Kurir ({{ $order->shipping_city }})</span>
                                    @endif
                                </td>

                                <!-- Total -->
                                <td class="fw-bold text-dark">
                                    {{ $order->formatted_total }}
                                </td>

                                <!-- Status -->
                                <td>
                                    <span class="badge {{ $order->status_badge_class }} px-2 py-1">
                                        {{ $order->status_label }}
                                    </span>
                                </td>

                                <!-- Action Buttons -->
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('orders.show', $order->order_number) }}" class="btn btn-outline-secondary btn-sm" title="Lihat Detail & Lacak">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="btn btn-outline-dark btn-sm" title="Cetak Invoice">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="card card-coffee border-0 shadow-sm p-5">
            <x-empty-state
                icon="bi-cup-hot"
                title="Belum Ada Riwayat Pesanan"
                message="Anda belum pernah melakukan pemesanan di Rasa Kopi. Coba racikan kopi terbaik kami hari ini!"
                actionText="Jelajahi Menu Kopi"
                :actionUrl="route('catalog.index')"
            />
        </div>
    @endif
</div>

@endsection
