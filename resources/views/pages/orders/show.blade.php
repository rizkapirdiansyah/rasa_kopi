@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number . ' - RASA KOPI')

@section('content')

<div class="container py-4 mb-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('orders.index') }}" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left"></i> Kembali</a>
                <span class="text-muted small">•</span>
                <span class="badge {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
            </div>
            <h1 class="h3 fw-bold font-serif text-dark mb-0">Pesanan #{{ $order->order_number }}</h1>
            <small class="text-muted">Dibuat pada: {{ $order->created_at->format('d F Y, H:i') }} WIB</small>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="btn btn-outline-dark btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-printer"></i> Cetak Invoice
            </a>

            <a href="https://wa.me/{{ $storeWhatsApp }}?text={{ $whatsappMessage }}" target="_blank" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-whatsapp"></i> Tanya Status via WA
            </a>

            @if($order->canBeCancelled())
                <form action="{{ route('orders.cancel', $order->order_number) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-x-circle"></i> Batalkan Pesanan
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Visual Order Tracking Timeline -->
    <div class="card card-coffee border-0 shadow-sm p-4 mb-4">
        <h6 class="fw-bold font-serif text-dark mb-4">Pelacakan Status Pembuatan &amp; Pengiriman</h6>

        @php
            $steps = [
                'menunggu_pembayaran' => ['title' => 'Menunggu Pembayaran', 'icon' => 'bi-wallet2'],
                'dibayar' => ['title' => 'Pembayaran Diterima', 'icon' => 'bi-check-circle'],
                'diproses' => ['title' => 'Diproses Barista', 'icon' => 'bi-cup-hot'],
                $order->shipping_type === 'pickup' ? 'siap_diambil' : 'dikirim' => [
                    'title' => $order->shipping_type === 'pickup' ? 'Siap Diambil' : 'Sedang Dikirim',
                    'icon' => $order->shipping_type === 'pickup' ? 'bi-shop' : 'bi-truck'
                ],
                'selesai' => ['title' => 'Pesanan Selesai', 'icon' => 'bi-patch-check-fill'],
            ];

            $statusOrder = ['pending', 'menunggu_pembayaran', 'dibayar', 'diproses', 'siap_diambil', 'dikirim', 'selesai'];
            $currentIndex = array_search($order->status, $statusOrder);
            if ($currentIndex === false) { $currentIndex = -1; }
        @endphp

        @if($order->status === 'dibatalkan')
            <div class="alert alert-danger mb-0">
                <i class="bi bi-x-octagon-fill me-2 fs-5"></i>
                <strong>Pesanan Dibatalkan.</strong> Stok produk telah dikembalikan ke sistem.
            </div>
        @else
            <div class="row g-2 text-center">
                @foreach($steps as $key => $step)
                    @php
                        $stepIndex = array_search($key, $statusOrder);
                        $isCompleted = $currentIndex >= $stepIndex;
                        $isCurrent = $order->status === $key;
                    @endphp
                    <div class="col">
                        <div class="d-flex flex-column align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 {{ $isCurrent ? 'bg-caramel text-white shadow' : ($isCompleted ? 'bg-success text-white' : 'bg-light text-muted border') }}" style="width: 44px; height: 44px;">
                                <i class="bi {{ $step['icon'] }} fs-5"></i>
                            </div>
                            <span class="small fw-bold {{ $isCurrent ? 'text-caramel' : ($isCompleted ? 'text-dark' : 'text-muted') }}">
                                {{ $step['title'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="row g-4">
        <!-- Items List -->
        <div class="col-lg-8">
            <div class="card card-coffee border-0 shadow-sm overflow-hidden mb-4">
                <div class="p-3 border-bottom bg-light">
                    <h6 class="fw-bold mb-0">Rincian Menu Kopi</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="small text-muted text-uppercase">
                            <tr>
                                <th scope="col" class="ps-3">Menu</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col" class="text-end pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            @if($item->product)
                                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="rounded shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <span class="fw-bold text-dark d-block">{{ $item->product_name }}</span>
                                                @if($order->isCompleted() && $item->product)
                                                    <a href="{{ route('catalog.show', $item->product->slug) }}#reviews" class="small text-caramel text-decoration-none">
                                                        <i class="bi bi-star me-1"></i> Beri Ulasan
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ $item->formatted_price }}</td>
                                    <td>{{ $item->quantity }}x</td>
                                    <td class="text-end pe-3 fw-bold">{{ $item->formatted_subtotal }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Price summary -->
                <div class="p-3 bg-light border-top">
                    <div class="d-flex justify-content-between mb-1 small text-muted">
                        <span>Subtotal:</span>
                        <span>{{ $order->formatted_subtotal }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-1 small text-success">
                            <span>Diskon ({{ $order->coupon_code }}):</span>
                            <span>- {{ $order->formatted_discount }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Ongkos Kirim:</span>
                        <span>{{ $order->formatted_shipping_cost }}</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2 border-top">
                        <strong class="text-dark">Total Transaksi:</strong>
                        <strong class="text-caramel fs-5">{{ $order->formatted_total }}</strong>
                    </div>
                </div>
            </div>

            <!-- Payment Information & Proof Upload -->
            <div class="card card-coffee border-0 shadow-sm p-4 mb-4">
                <h6 class="fw-bold font-serif text-dark mb-3">Informasi Pembayaran</h6>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Metode Pembayaran:</small>
                        <strong class="text-dark">{{ $order->payment_method_name }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Status Pembayaran:</small>
                        @if($order->payment)
                            <span class="badge {{ $order->payment->status === 'verified' ? 'bg-success' : ($order->payment->status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                {{ $order->payment->status_label }}
                            </span>
                        @else
                            <span class="badge bg-secondary">Belum Dibayar</span>
                        @endif
                    </div>
                </div>

                @if($order->payment && $order->payment->proof_image)
                    <div class="p-3 border rounded-3 bg-light mb-3">
                        <div class="small fw-bold mb-2">Bukti Pembayaran yang Diunggah:</div>
                        <img src="{{ $order->payment->proof_image_url }}" alt="Bukti Transfer" class="img-fluid rounded border" style="max-height: 250px;">
                        @if($order->payment->rejection_reason)
                            <div class="alert alert-danger mt-2 small mb-0">
                                <strong>Catatan Penolakan:</strong> {{ $order->payment->rejection_reason }}
                            </div>
                        @endif
                    </div>
                @endif

                @if($order->status === 'menunggu_pembayaran')
                    <div class="border-top pt-3">
                        <h6 class="fw-bold small text-dark mb-2">Unggah / Unggah Ulang Bukti Transfer:</h6>
                        <form action="{{ route('orders.payment-proof', $order->order_number) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-2 align-items-center">
                                <div class="col-md-5">
                                    <input type="file" name="proof_image" class="form-control form-control-sm" accept="image/*" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="sender_name" class="form-control form-control-sm" placeholder="Nama Pemilik Rekening">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-caramel btn-sm w-100">Upload Bukti</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Shipping & Notes Info -->
        <div class="col-lg-4">
            <!-- Delivery Info -->
            <div class="card card-coffee border-0 shadow-sm p-4 mb-4">
                <h6 class="fw-bold font-serif text-dark mb-3">Tujuan &amp; Pengiriman</h6>

                <div class="mb-3">
                    <small class="text-muted d-block">Tipe Pengiriman:</small>
                    <strong>
                        @if($order->shipping_type === 'pickup')
                            <i class="bi bi-shop text-caramel me-1"></i> Ambil Sendiri di Toko
                        @else
                            <i class="bi bi-bicycle text-caramel me-1"></i> Pengiriman Kurir
                        @endif
                    </strong>
                </div>

                @if($order->shipping_type === 'pickup')
                    <div class="mb-3">
                        <small class="text-muted d-block">Waktu Pengambilan:</small>
                        <span class="badge bg-light text-dark border">{{ $order->pickup_time ?? 'Segera' }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Alamat Kedai:</small>
                        <span class="small text-muted">Jl. Kopi Kenikmatan No. 18, Senopati, Jakarta Selatan</span>
                    </div>
                @else
                    <div class="mb-2">
                        <small class="text-muted d-block">Alamat Pengiriman:</small>
                        <p class="small text-dark mb-0">{{ $order->shipping_address }}</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Kota:</small>
                        <span class="small fw-semibold">{{ $order->shipping_city }}</span>
                    </div>
                @endif

                <hr>

                <div class="mb-2">
                    <small class="text-muted d-block">Nama Penerima:</small>
                    <strong class="text-dark">{{ $order->customer_name }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">WhatsApp / No. HP:</small>
                    <span class="small text-dark">{{ $order->customer_phone }}</span>
                </div>
                <div>
                    <small class="text-muted d-block">Email:</small>
                    <span class="small text-dark">{{ $order->customer_email }}</span>
                </div>

                @if($order->customer_notes)
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted d-block">Catatan Pelanggan:</small>
                        <span class="small text-muted fst-italic">"{{ $order->customer_notes }}"</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
