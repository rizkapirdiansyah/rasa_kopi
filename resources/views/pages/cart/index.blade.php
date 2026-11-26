@extends('layouts.app')

@section('title', 'Keranjang Belanja - RASA KOPI')

@section('content')

<div class="container py-4 mb-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold font-serif text-dark mb-0">Keranjang Belanja</h1>
            <p class="text-muted small mb-0">Periksa kembali pesanan Anda sebelum lanjut ke pembayaran.</p>
        </div>
        <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Tambah Menu Lain
        </a>
    </div>

    @if($items->isNotEmpty())
        <div class="row g-4">
            <!-- Cart Items Table -->
            <div class="col-lg-8">
                <div class="card card-coffee border-0 shadow-sm overflow-hidden">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th scope="col" class="ps-4">Menu Kopi</th>
                                    <th scope="col">Harga</th>
                                    <th scope="col" style="width: 150px;">Jumlah</th>
                                    <th scope="col">Subtotal</th>
                                    <th scope="col" class="text-center pe-4" style="width: 60px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $productId => $item)
                                    <tr>
                                        <!-- Product info -->
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}" class="rounded-3 shadow-sm" style="width: 64px; height: 64px; object-fit: cover;">
                                                <div>
                                                    <h6 class="fw-bold mb-1">
                                                        <a href="{{ route('catalog.show', $item['product']->slug) }}" class="text-dark text-decoration-none">
                                                            {{ $item['product']->name }}
                                                        </a>
                                                    </h6>
                                                    <span class="badge badge-cream text-dark">{{ $item['product']->category->name ?? 'Kopi' }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Price -->
                                        <td class="text-muted">
                                            {{ $item['product']->formatted_price }}
                                        </td>

                                        <!-- Quantity Form -->
                                        <td>
                                            <form action="{{ route('cart.update', $productId) }}" method="POST" class="d-flex align-items-center m-0">
                                                @csrf
                                                <div class="input-group input-group-sm" style="max-width: 120px;">
                                                    <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}" class="btn btn-outline-secondary">-</button>
                                                    <input type="text" class="form-control text-center bg-white" value="{{ $item['quantity'] }}" readonly>
                                                    <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}" class="btn btn-outline-secondary" {{ $item['quantity'] >= $item['product']->stock ? 'disabled' : '' }}>+</button>
                                                </div>
                                            </form>
                                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Stok: {{ $item['product']->stock }}</small>
                                        </td>

                                        <!-- Subtotal -->
                                        <td class="fw-bold text-caramel">
                                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                        </td>

                                        <!-- Remove button -->
                                        <td class="text-center pe-4">
                                            <form action="{{ route('cart.remove', $productId) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus item ini dari keranjang?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm text-danger border-0 p-1" title="Hapus dari keranjang">
                                                    <i class="bi bi-trash fs-5"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Promo Coupon Form -->
                <div class="card card-coffee border-0 shadow-sm p-4 mt-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-ticket-perforated text-caramel me-1"></i> Punya Kupon Diskon?</h6>
                    @if($coupon)
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background-color: #f7efe5; border: 1px dashed var(--caramel);">
                            <div>
                                <span class="badge bg-success me-2">Aktif</span>
                                <strong>{{ $coupon->code }}</strong> - {{ $coupon->name }}
                                <div class="text-muted small mt-1">Potongan sebesar: <strong>Rp {{ number_format($discount, 0, ',', '.') }}</strong></div>
                            </div>
                            <form action="{{ route('cart.coupon.remove') }}" method="POST" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus Kupon</button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('cart.coupon.apply') }}" method="POST" class="d-flex gap-2">
                            @csrf
                            <input type="text" name="coupon_code" class="form-control text-uppercase" placeholder="Masukkan kode promo (misal: NGOPIHEMAT)" required>
                            <button type="submit" class="btn btn-outline-caramel px-4">Terapkan</button>
                        </form>
                        <small class="text-muted mt-2 d-block">Tips: Coba gunakan kode <strong>NGOPIHEMAT</strong> atau <strong>KOPI20</strong>!</small>
                    @endif
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="col-lg-4">
                <div class="card card-coffee border-0 shadow-sm p-4 sticky-top" style="top: 100px;">
                    <h5 class="fw-bold font-serif text-dark mb-3">Ringkasan Pesanan</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal ({{ app(\App\Services\CartService::class)->getItemCount() }} item)</span>
                        <span class="fw-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    @if($discount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Diskon Kupon</span>
                            <span>- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-3 text-muted small">
                        <span>Ongkos Kirim</span>
                        <span>Dihitung saat checkout</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-baseline mb-4">
                        <span class="h6 fw-bold mb-0">Total Belanja</span>
                        <span class="h4 fw-bold text-caramel mb-0">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    @auth
                        <a href="{{ route('checkout.index') }}" class="btn btn-caramel btn-lg w-100 py-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <span>Lanjut ke Checkout</span> <i class="bi bi-arrow-right"></i>
                        </a>
                    @else
                        <a href="{{ route('checkout.index') }}" class="btn btn-caramel btn-lg w-100 py-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <span>Lanjut ke Checkout</span> <i class="bi bi-arrow-right"></i>
                        </a>
                        <small class="text-muted text-center d-block mt-2">Anda akan diminta login / mengisi data saat checkout.</small>
                    @endauth

                    <div class="mt-4 pt-3 border-top text-center text-muted small">
                        <div class="d-flex justify-content-center gap-3 mb-2 fs-5">
                            <i class="bi bi-shield-check text-success" title="Pembayaran Aman"></i>
                            <i class="bi bi-truck text-primary" title="Pengiriman Cepat"></i>
                            <i class="bi bi-cup-hot text-caramel" title="Fresh Roasting"></i>
                        </div>
                        Pesanan Anda diproses langsung oleh barista Rasa Kopi dengan standar kebersihan terbaik.
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card card-coffee border-0 shadow-sm p-5">
            <x-empty-state
                icon="bi-cart-x"
                title="Keranjang Belanja Anda Kosong"
                message="Anda belum memilih menu kopi apapun. Yuk jelajahi menu spesial kami sekarang!"
                actionText="Jelajahi Menu Kopi"
                :actionUrl="route('catalog.index')"
            />
        </div>
    @endif
</div>

@endsection
