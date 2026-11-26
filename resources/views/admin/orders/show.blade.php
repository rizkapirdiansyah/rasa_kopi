@extends('layouts.admin')

@section('page_title', 'Kelola Pesanan: ' . $order->order_number)

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Pesanan
        </a>
        <span class="badge {{ $order->status_badge_class }} fs-6">{{ $order->status_label }}</span>
    </div>

    <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-outline-dark btn-sm">
        <i class="bi bi-printer me-1"></i> Cetak Invoice
    </a>
</div>

<div class="row g-4">
    <!-- Left Column: Items, Status Action, Payment Verification -->
    <div class="col-lg-8">
        <!-- 1. Form Update Status Pesanan -->
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
            <h6 class="fw-bold text-dark font-serif mb-3"><i class="bi bi-arrow-repeat text-caramel me-1"></i> Perbarui Status Pesanan</h6>

            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Pilih Status Baru:</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="menunggu_pembayaran" {{ $order->status === 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                            <option value="dibayar" {{ $order->status === 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                            <option value="diproses" {{ $order->status === 'diproses' ? 'selected' : '' }}>Diproses (Barista Meracik)</option>
                            <option value="siap_diambil" {{ $order->status === 'siap_diambil' ? 'selected' : '' }}>Siap Diambil di Toko</option>
                            <option value="dikirim" {{ $order->status === 'dikirim' ? 'selected' : '' }}>Sedang Dikirim Kurir</option>
                            <option value="selesai" {{ $order->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ $order->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan (Kembalikan Stok)</option>
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Catatan Admin (Opsional):</label>
                        <input type="text" name="admin_notes" class="form-control" value="{{ old('admin_notes', $order->admin_notes) }}" placeholder="Catatan internal barista/admin...">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-caramel w-100">Update</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- 2. Verifikasi Pembayaran & Bukti Transfer -->
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
            <h6 class="fw-bold text-dark font-serif mb-3"><i class="bi bi-credit-card text-caramel me-1"></i> Verifikasi Pembayaran</h6>

            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <small class="text-muted d-block">Metode Pembayaran:</small>
                    <strong class="text-dark">{{ $order->payment_method_name }}</strong>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Status Verifikasi:</small>
                    @if($order->payment)
                        <span class="badge {{ $order->payment->status === 'verified' ? 'bg-success' : ($order->payment->status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }} fs-6">
                            {{ $order->payment->status_label }}
                        </span>
                        @if($order->payment->verifiedByUser)
                            <small class="text-muted d-block mt-1">Diverifikasi oleh: {{ $order->payment->verifiedByUser->name }}</small>
                        @endif
                    @else
                        <span class="badge bg-secondary">Belum ada record pembayaran</span>
                    @endif
                </div>
            </div>

            @if($order->payment && $order->payment->proof_image)
                <div class="p-3 border rounded-3 bg-light mb-4">
                    <div class="small fw-bold mb-2">Foto Bukti Transfer:</div>
                    <a href="{{ $order->payment->proof_image_url }}" target="_blank">
                        <img src="{{ $order->payment->proof_image_url }}" alt="Bukti Transfer" class="img-fluid rounded border shadow-sm" style="max-height: 300px;">
                    </a>
                    <div class="mt-2 small text-muted">
                        <div>Nama Pengirim: <strong>{{ $order->payment->sender_name ?? '-' }}</strong></div>
                        <div>Bank Pengirim: <strong>{{ $order->payment->sender_bank ?? '-' }}</strong></div>
                    </div>

                    <!-- Approval Form -->
                    <div class="border-top pt-3 mt-3">
                        <form action="{{ route('admin.orders.verify-payment', $order->id) }}" method="POST">
                            @csrf
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm px-3" onclick="return confirm('Verifikasi pembayaran ini sebagai SAH dan ubah pesanan ke Diproses?');">
                                    <i class="bi bi-check-circle me-1"></i> Verifikasi Pembayaran (Valid)
                                </button>

                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="collapse" data-bs-target="#rejectBox">
                                    <i class="bi bi-x-circle me-1"></i> Tolak Bukti Transfer
                                </button>
                            </div>

                            <div class="collapse mt-3" id="rejectBox">
                                <div class="p-3 bg-white border rounded">
                                    <label class="form-label small fw-bold">Alasan Penolakan:</label>
                                    <textarea name="rejection_reason" rows="2" class="form-control form-control-sm mb-2" placeholder="Contoh: Nominal transfer kurang, foto buram..."></textarea>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Konfirmasi Tolak</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- 3. Rincian Item Pesanan -->
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white mb-4">
            <div class="p-3 border-bottom bg-light">
                <h6 class="fw-bold mb-0">Rincian Menu Kopi yang Dipesan</h6>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="small text-muted text-uppercase">
                        <tr>
                            <th scope="col" class="ps-3">Nama Menu</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col" class="text-end pe-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td class="ps-3 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($item->product)
                                            <img src="{{ $item->product->image_url }}" alt="" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <strong class="text-dark">{{ $item->product_name }}</strong>
                                            <small class="text-muted d-block">{{ $item->product->category->name ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted small">{{ $item->formatted_price }}</td>
                                <td class="small">{{ $item->quantity }}x</td>
                                <td class="text-end pe-3 fw-bold">{{ $item->formatted_subtotal }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 bg-light border-top">
                <div class="d-flex justify-content-between mb-1 small text-muted">
                    <span>Subtotal Produk:</span>
                    <span>{{ $order->formatted_subtotal }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-1 small text-success">
                        <span>Diskon Kupon ({{ $order->coupon_code }}):</span>
                        <span>- {{ $order->formatted_discount }}</span>
                    </div>
                @endif
                <div class="d-flex justify-content-between mb-2 small text-muted">
                    <span>Ongkos Kirim:</span>
                    <span>{{ $order->formatted_shipping_cost }}</span>
                </div>
                <div class="d-flex justify-content-between pt-2 border-top">
                    <strong class="text-dark">Total Pembayaran:</strong>
                    <strong class="text-caramel fs-5">{{ $order->formatted_total }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Customer Info, Shipping Details -->
    <div class="col-lg-4">
        <!-- Customer Info Card -->
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
            <h6 class="fw-bold font-serif text-dark mb-3"><i class="bi bi-person text-caramel me-1"></i> Data Pelanggan</h6>

            <div class="mb-2">
                <small class="text-muted d-block">Nama Lengkap:</small>
                <strong class="text-dark">{{ $order->customer_name }}</strong>
            </div>

            <div class="mb-2">
                <small class="text-muted d-block">Nomor Telepon / WA:</small>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" class="text-decoration-none text-success fw-bold">
                    <i class="bi bi-whatsapp me-1"></i> {{ $order->customer_phone }}
                </a>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Alamat Email:</small>
                <span class="text-dark small">{{ $order->customer_email }}</span>
            </div>

            @if($order->user)
                <a href="{{ route('admin.customers.show', $order->user_id) }}" class="btn btn-outline-secondary btn-sm w-100">
                    Lihat Profil Pelanggan
                </a>
            @endif
        </div>

        <!-- Shipping Info Card -->
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
            <h6 class="fw-bold font-serif text-dark mb-3"><i class="bi bi-truck text-caramel me-1"></i> Info Pengiriman</h6>

            <div class="mb-3">
                <small class="text-muted d-block">Tipe Pengiriman:</small>
                <span class="badge {{ $order->shipping_type === 'pickup' ? 'bg-primary' : 'bg-info text-dark' }}">
                    {{ $order->shipping_type === 'pickup' ? 'Ambil di Toko' : 'Pengiriman Kurir' }}
                </span>
            </div>

            @if($order->shipping_type === 'pickup')
                <div class="mb-2">
                    <small class="text-muted d-block">Waktu Pengambilan:</small>
                    <strong class="text-dark">{{ $order->pickup_time ?? 'Segera' }}</strong>
                </div>
            @else
                <div class="mb-2">
                    <small class="text-muted d-block">Alamat Lengkap:</small>
                    <p class="small text-dark mb-0">{{ $order->shipping_address }}</p>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Kota:</small>
                    <span class="small fw-semibold">{{ $order->shipping_city }}</span>
                </div>
            @endif

            @if($order->customer_notes)
                <div class="mt-3 pt-3 border-top">
                    <small class="text-muted d-block">Catatan Pelanggan:</small>
                    <div class="p-2 bg-light rounded small fst-italic">"{{ $order->customer_notes }}"</div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
