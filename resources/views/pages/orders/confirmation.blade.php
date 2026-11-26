@extends('layouts.app')

@section('title', 'Konfirmasi Pesanan #' . $order->order_number . ' - RASA KOPI')

@section('content')

<div class="container py-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Card -->
            <div class="card card-coffee border-0 shadow-sm p-4 p-md-5 text-center mb-4">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white shadow" style="width: 72px; height: 72px;">
                        <i class="bi bi-check-lg fs-1"></i>
                    </span>
                </div>

                <span class="badge badge-caramel mx-auto mb-2 px-3 py-2 fs-6">Pesanan Diterima</span>
                <h2 class="fw-bold font-serif text-dark mb-1">Terima Kasih, {{ $order->customer_name }}!</h2>
                <p class="text-muted small mb-4">Pesanan kopi Anda telah berhasil dibuat dan tercatat di sistem kami.</p>

                <!-- Order Number Banner -->
                <div class="p-3 rounded-3 mb-4 mx-auto" style="background-color: #f7efe5; max-width: 480px; border: 1.5px dashed var(--caramel);">
                    <small class="text-muted d-block mb-1">Nomor Pesanan Unik:</small>
                    <span class="h4 fw-bold text-dark font-monospace mb-0">{{ $order->order_number }}</span>
                </div>

                <div class="row g-3 text-start small mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <span class="text-muted d-block">Metode Pengiriman:</span>
                            <strong class="text-dark">
                                @if($order->shipping_type === 'pickup')
                                    <i class="bi bi-shop text-caramel"></i> Ambil di Toko (Waktu: {{ $order->pickup_time ?? 'Segera' }})
                                @else
                                    <i class="bi bi-bicycle text-caramel"></i> Pengiriman Kurir ke {{ $order->shipping_city }}
                                @endif
                            </strong>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <span class="text-muted d-block">Total Pembayaran:</span>
                            <strong class="text-caramel fs-5">{{ $order->formatted_total }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Payment Instruction if Bank Transfer -->
                @if($order->paymentMethod && $order->paymentMethod->code !== 'cash')
                    <div class="card p-4 border rounded-3 mb-4 text-start" style="background-color: #fff9f3; border-color: #edd6c8 !important;">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-wallet2 text-caramel me-2"></i> Petunjuk Pembayaran Transfer Bank:</h6>
                        <p class="small text-muted mb-3">{{ $order->paymentMethod->instructions }}</p>

                        <div class="d-flex flex-wrap justify-content-between align-items-center p-3 bg-white rounded border mb-3">
                            <div>
                                <small class="text-muted d-block">Bank:</small>
                                <strong>{{ $order->paymentMethod->name }}</strong>
                            </div>
                            <div>
                                <small class="text-muted d-block">Nomor Rekening:</small>
                                <span class="h5 fw-bold font-monospace text-caramel mb-0">{{ $order->paymentMethod->account_number }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Atas Nama:</small>
                                <strong>{{ $order->paymentMethod->account_name }}</strong>
                            </div>
                        </div>

                        <!-- Upload Proof Form -->
                        <h6 class="fw-bold text-dark mb-2 mt-2">Unggah Bukti Pembayaran:</h6>
                        <form action="{{ route('orders.payment-proof', $order->order_number) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-2 align-items-center">
                                <div class="col-md-5">
                                    <input type="file" name="proof_image" class="form-control form-control-sm" accept="image/*" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="sender_name" class="form-control form-control-sm" placeholder="Nama Pengirim (opsional)">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-caramel btn-sm w-100">Upload Bukti</button>
                                </div>
                            </div>
                            <small class="text-muted mt-1 d-block">Format: JPG, PNG, WEBP (Maksimal 2MB).</small>
                        </form>
                    </div>
                @else
                    <div class="alert alert-info text-start small mb-4">
                        <i class="bi bi-info-circle me-1"></i> Anda memilih metode pembayaran tunai saat pengambilan di toko. Silakan siapkan uang pas sebesar <strong>{{ $order->formatted_total }}</strong> di kasir gerai kami.
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="btn btn-outline-dark px-4 py-2">
                        <i class="bi bi-printer me-1"></i> Cetak Invoice
                    </a>

                    <a href="https://wa.me/{{ $storeWhatsApp }}?text={{ $whatsappMessage }}" target="_blank" class="btn btn-success px-4 py-2">
                        <i class="bi bi-whatsapp me-1"></i> Konfirmasi via WhatsApp
                    </a>

                    <a href="{{ route('orders.index') }}" class="btn btn-caramel px-4 py-2">
                        <i class="bi bi-bag-check me-1"></i> Riwayat Pesanan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
