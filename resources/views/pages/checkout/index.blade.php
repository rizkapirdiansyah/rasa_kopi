@extends('layouts.app')

@section('title', 'Checkout Pesanan - RASA KOPI')

@section('content')

<div class="container py-4 mb-5">
    <div class="mb-4">
        <h1 class="h3 fw-bold font-serif text-dark mb-0">Checkout Pesanan</h1>
        <p class="text-muted small">Lengkapi data diri dan pilihan pengiriman untuk menyelesaikan pesanan Anda.</p>
    </div>

    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Customer Data, Shipping, Payment -->
            <div class="col-lg-8">
                <!-- 1. Data Pelanggan -->
                <div class="card card-coffee border-0 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold font-serif text-dark mb-3">
                        <span class="badge bg-caramel me-2">1</span> Identitas Pelanggan
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $user->name ?? '') }}" required placeholder="Contoh: Budi Santoso">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email', $user->email ?? '') }}" required placeholder="email@anda.com">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Nomor WhatsApp / Telepon <span class="text-danger">*</span></label>
                            <input type="tel" name="customer_phone" class="form-control" value="{{ old('customer_phone', $user->phone ?? '') }}" required placeholder="Contoh: 081234567890">
                            <small class="text-muted">Untuk konfirmasi pesanan dan status pembuatan kopi via WhatsApp.</small>
                        </div>
                    </div>
                </div>

                <!-- 2. Pilihan Pengiriman -->
                <div class="card card-coffee border-0 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold font-serif text-dark mb-3">
                        <span class="badge bg-caramel me-2">2</span> Metode Pengiriman
                    </h5>

                    <div class="row g-3 mb-4">
                        <!-- Pick-up -->
                        <div class="col-md-6">
                            <div class="form-check p-3 border rounded-3 h-100 option-card" id="pickupOptionCard">
                                <input class="form-check-input" type="radio" name="shipping_type" id="shippingPickup" value="pickup" {{ old('shipping_type', 'pickup') === 'pickup' ? 'checked' : '' }} onchange="toggleShippingMode('pickup')">
                                <label class="form-check-label w-100 ms-2" for="shippingPickup">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="text-dark"><i class="bi bi-shop text-caramel me-1"></i> Ambil di Toko (Pick-up)</strong>
                                        <span class="badge bg-success">Gratis (Rp 0)</span>
                                    </div>
                                    <small class="text-muted d-block">Ambil pesanan langsung di gerai kami tanpa antre.</small>
                                </label>
                            </div>
                        </div>

                        <!-- Delivery -->
                        <div class="col-md-6">
                            <div class="form-check p-3 border rounded-3 h-100 option-card" id="deliveryOptionCard">
                                <input class="form-check-input" type="radio" name="shipping_type" id="shippingDelivery" value="delivery" {{ old('shipping_type') === 'delivery' ? 'checked' : '' }} onchange="toggleShippingMode('delivery')">
                                <label class="form-check-label w-100 ms-2" for="shippingDelivery">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="text-dark"><i class="bi bi-bicycle text-caramel me-1"></i> Pengiriman Kurir</strong>
                                        <span class="badge badge-cream text-dark">Rp {{ number_format($shippingFlatRate, 0, ',', '.') }}</span>
                                    </div>
                                    <small class="text-muted d-block">Kurir mengantar pesanan langsung ke alamat Anda.</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Pick-up Fields -->
                    <div id="pickupFields" class="{{ old('shipping_type', 'pickup') === 'pickup' ? '' : 'd-none' }}">
                        <label class="form-label small fw-bold">Perkiraan Waktu Pengambilan di Toko <span class="text-danger">*</span></label>
                        <select name="pickup_time" class="form-select" id="pickupTimeSelect">
                            <option value="15 Menit Lagi" {{ old('pickup_time') === '15 Menit Lagi' ? 'selected' : '' }}>15 Menit Lagi (Segera Disiapkan)</option>
                            <option value="30 Menit Lagi" {{ old('pickup_time') === '30 Menit Lagi' ? 'selected' : '' }}>30 Menit Lagi</option>
                            <option value="1 Jam Lagi" {{ old('pickup_time') === '1 Jam Lagi' ? 'selected' : '' }}>1 Jam Lagi</option>
                            <option value="Siang Ini (12:00 - 13:00)" {{ old('pickup_time') === 'Siang Ini (12:00 - 13:00)' ? 'selected' : '' }}>Siang Ini (12:00 - 13:00)</option>
                            <option value="Sore Ini (16:00 - 18:00)" {{ old('pickup_time') === 'Sore Ini (16:00 - 18:00)' ? 'selected' : '' }}>Sore Ini (16:00 - 18:00)</option>
                        </select>
                        <small class="text-muted d-block mt-1">Alamat gerai: Jl. Kopi Kenikmatan No. 18, Senopati, Jakarta Selatan.</small>
                    </div>

                    <!-- Delivery Fields -->
                    <div id="deliveryFields" class="{{ old('shipping_type') === 'delivery' ? '' : 'd-none' }}">
                        @if($savedAddresses->isNotEmpty())
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Gunakan Alamat Tersimpan:</label>
                                <select class="form-select" id="savedAddressSelector" onchange="applySavedAddress(this)">
                                    <option value="">-- Pilih dari Alamat Tersimpan --</option>
                                    @foreach($savedAddresses as $addr)
                                        <option value="{{ $addr->id }}" data-address="{{ $addr->address_line }}" data-city="{{ $addr->city }}">
                                            {{ $addr->label }} - {{ $addr->recipient_name }} ({{ $addr->city }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alamat Lengkap Pengiriman <span class="text-danger">*</span></label>
                            <textarea name="shipping_address" id="shippingAddress" rows="2" class="form-control" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, patokan...">{{ old('shipping_address') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Kota / Wilayah <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_city" id="shippingCity" class="form-control" value="{{ old('shipping_city', 'Jakarta Selatan') }}" placeholder="Contoh: Jakarta Selatan">
                        </div>
                    </div>
                </div>

                <!-- 3. Pilihan Pembayaran -->
                <div class="card card-coffee border-0 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold font-serif text-dark mb-3">
                        <span class="badge bg-caramel me-2">3</span> Metode Pembayaran
                    </h5>

                    <div class="d-flex flex-column gap-3">
                        @foreach($paymentMethods as $pm)
                            <div class="form-check p-3 border rounded-3">
                                <input class="form-check-input" type="radio" name="payment_method_id" id="pm_{{ $pm->id }}" value="{{ $pm->id }}" {{ old('payment_method_id', $paymentMethods->first()->id ?? null) == $pm->id ? 'checked' : '' }}>
                                <label class="form-check-label w-100 ms-2" for="pm_{{ $pm->id }}">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-dark"><i class="bi {{ $pm->icon ?? 'bi-credit-card' }} text-caramel me-2"></i> {{ $pm->name }}</span>
                                        @if($pm->account_number)
                                            <span class="badge bg-light text-dark border">No. Rek: {{ $pm->account_number }}</span>
                                        @endif
                                    </div>
                                    @if($pm->account_name)
                                        <div class="small text-muted">a.n. {{ $pm->account_name }}</div>
                                    @endif
                                    <small class="text-muted d-block mt-1">{{ $pm->instructions }}</small>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 4. Catatan Tambahan -->
                <div class="card card-coffee border-0 shadow-sm p-4">
                    <h5 class="fw-bold font-serif text-dark mb-3">
                        <span class="badge bg-caramel me-2">4</span> Catatan Khusus Barista
                    </h5>
                    <textarea name="customer_notes" rows="2" class="form-control" placeholder="Contoh: Gula sedikit (less sugar), es batu dipisah, jangan terlalu manis...">{{ old('customer_notes') }}</textarea>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="col-lg-4">
                <div class="card card-coffee border-0 shadow-sm p-4 sticky-top" style="top: 100px;">
                    <h5 class="fw-bold font-serif text-dark mb-3">Pesanan Anda</h5>

                    <!-- Items List -->
                    <div class="mb-3" style="max-height: 250px; overflow-y: auto;">
                        @foreach($items as $item)
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark border">{{ $item['quantity'] }}x</span>
                                    <div>
                                        <div class="small fw-bold text-dark">{{ $item['product']->name }}</div>
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $item['product']->formatted_price }}</small>
                                    </div>
                                </div>
                                <span class="small fw-semibold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Calculation breakdown -->
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Subtotal Produk</span>
                        <span class="fw-semibold text-dark">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    @if($discount > 0)
                        <div class="d-flex justify-content-between mb-2 small text-success">
                            <span>Diskon Kupon ({{ $coupon->code }})</span>
                            <span class="fw-bold">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-3 small text-muted">
                        <span>Ongkos Kirim</span>
                        <span id="displayShippingCost" class="fw-semibold text-dark">Gratis (Rp 0)</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-baseline mb-4">
                        <span class="h6 fw-bold mb-0">Total Pembayaran</span>
                        <span class="h4 fw-bold text-caramel mb-0" id="displayGrandTotal">
                            Rp {{ number_format(max(0, $subtotal - $discount), 0, ',', '.') }}
                        </span>
                    </div>

                    <button type="submit" class="btn btn-caramel btn-lg w-100 py-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-shield-check"></i>
                        <span>Konfirmasi &amp; Buat Pesanan</span>
                    </button>

                    <div class="mt-3 text-center text-muted small">
                        <i class="bi bi-lock me-1"></i> Data transaksi Anda diproses secara aman.
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const subtotal = {{ (float) $subtotal }};
    const discount = {{ (float) $discount }};
    const shippingFlatRate = {{ (float) $shippingFlatRate }};

    function toggleShippingMode(mode) {
        const pickupFields = document.getElementById('pickupFields');
        const deliveryFields = document.getElementById('deliveryFields');
        const displayShipping = document.getElementById('displayShippingCost');
        const displayTotal = document.getElementById('displayGrandTotal');

        if (mode === 'delivery') {
            pickupFields.classList.add('d-none');
            deliveryFields.classList.remove('d-none');
            displayShipping.textContent = 'Rp ' + shippingFlatRate.toLocaleString('id-ID');
            const total = Math.max(0, (subtotal - discount) + shippingFlatRate);
            displayTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
        } else {
            pickupFields.classList.remove('d-none');
            deliveryFields.classList.add('d-none');
            displayShipping.textContent = 'Gratis (Rp 0)';
            const total = Math.max(0, (subtotal - discount));
            displayTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
    }

    function applySavedAddress(select) {
        const opt = select.options[select.selectedIndex];
        if (opt.value) {
            document.getElementById('shippingAddress').value = opt.getAttribute('data-address');
            document.getElementById('shippingCity').value = opt.getAttribute('data-city');
        }
    }

    // Initialize display on load
    document.addEventListener('DOMContentLoaded', function() {
        const isDelivery = document.getElementById('shippingDelivery').checked;
        if (isDelivery) {
            toggleShippingMode('delivery');
        }
    });
</script>

@endsection
