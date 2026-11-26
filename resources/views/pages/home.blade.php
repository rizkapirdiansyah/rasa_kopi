@extends('layouts.app')

@section('title', 'RASA KOPI - Cita Rasa Kopi Nusantara Terbaik')

@section('content')

<!-- Hero Banner Section -->
<section class="hero-coffee py-5 mb-5">
    <div class="container py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-7 text-center text-lg-start">
                <span class="badge badge-caramel mb-3 px-3 py-2 text-uppercase letter-spacing-1">
                    Authentic Indonesian Coffee
                </span>
                <h1 class="display-4 fw-bold font-serif mb-3 text-white">
                    Setiap Tegukan Membawa Cerita Kopi Nusantara
                </h1>
                <p class="lead text-light mb-4 opacity-90 pe-lg-4">
                    Dipetik dari perkebunan terbaik lereng nusantara, disangrai dengan presisi dan diracik oleh barista berpengalaman untuk menghadirkan rasa kopi sejati di setiap cangkir Anda.
                </p>
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                    <a href="{{ route('catalog.index') }}" class="btn btn-caramel btn-lg px-4 py-3 shadow-lg">
                        <i class="bi bi-cup-hot-fill me-2"></i> Pesan Sekarang
                    </a>
                    <a href="{{ route('about') }}" class="btn btn-outline-light btn-lg px-4 py-3">
                        Tentang Kami
                    </a>
                </div>

                <!-- Fast Info Highlights -->
                <div class="row mt-5 pt-3 border-top border-light border-opacity-25 text-start g-3">
                    <div class="col-4">
                        <div class="h3 fw-bold text-caramel mb-0 font-serif">100%</div>
                        <small class="text-light opacity-75">Biji Asli Nusantara</small>
                    </div>
                    <div class="col-4">
                        <div class="h3 fw-bold text-caramel mb-0 font-serif">Fresh</div>
                        <small class="text-light opacity-75">Roasted Daily</small>
                    </div>
                    <div class="col-4">
                        <div class="h3 fw-bold text-caramel mb-0 font-serif">★ 4.9</div>
                        <small class="text-light opacity-75">Ribuan Penikmat Kopi</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-block text-center">
                <div class="position-relative d-inline-block">
                    <img src="{{ asset('images/coffee-placeholder.svg') }}" alt="Rasa Kopi Hero" class="img-fluid rounded-4 shadow-lg border border-3 border-light border-opacity-25" style="max-height: 420px; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 translate-middle-x bg-white p-3 rounded-3 shadow text-dark text-start" style="width: 220px; margin-left: 50px; margin-bottom: -20px;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-success p-1"><i class="bi bi-check2"></i></span>
                            <span class="small fw-bold">Siap Disajikan</span>
                        </div>
                        <div class="small text-muted">Bisa Ambil di Toko atau Dikirim ke Rumah Anda!</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Active Promos Banner -->
@if($activeCoupons->isNotEmpty())
    <section class="container mb-5">
        <div class="p-4 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #f5ece1 0%, #edd6c8 100%); border: 1.5px dashed var(--caramel);">
            <div class="row align-items-center">
                <div class="col-md-8 text-center text-md-start mb-3 mb-md-0">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-1">
                        <i class="bi bi-gift-fill text-caramel fs-4"></i>
                        <h4 class="fw-bold mb-0 font-serif text-dark">Promo Spesial Hari Ini!</h4>
                    </div>
                    <p class="text-muted mb-0 small">
                        Gunakan kode promo berikut saat checkout untuk mendapatkan potongan harga spesial:
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-2 justify-content-center justify-content-md-start">
                        @foreach($activeCoupons as $coupon)
                            <span class="badge bg-white text-dark border border-secondary border-opacity-25 px-3 py-2 fs-6">
                                <i class="bi bi-tag-fill text-caramel me-1"></i>
                                <strong>{{ $coupon->code }}</strong>
                                <span class="text-muted small ms-1">({{ $coupon->type === 'percent' ? $coupon->value . '%' : 'Rp ' . number_format($coupon->value, 0, ',', '.') }})</span>
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-4 text-center text-md-end">
                    <a href="{{ route('catalog.index') }}" class="btn btn-coffee-dark px-4 py-2">
                        Gunakan Kupon Sekarang <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endif

<!-- Categories Section -->
<section class="container mb-5">
    <div class="text-center mb-4">
        <span class="text-caramel fw-bold text-uppercase small">Koleksi Rasa</span>
        <h2 class="fw-bold font-serif text-dark">Kategori Pilihan</h2>
        <p class="text-muted small mx-auto" style="max-width: 500px;">Temukan racikan kopi favorit Anda mulai dari seduhan espresso berkarakter hingga racikan signature kami.</p>
    </div>

    <div class="row g-3 justify-content-center">
        @foreach($categories as $category)
            <div class="col-6 col-md-3">
                <a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="text-decoration-none">
                    <div class="card card-coffee text-center p-3 h-100 border-0 shadow-sm" style="background-color: #ffffff;">
                        <div class="mb-2">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 54px; height: 54px; background-color: var(--cream-soft); color: var(--caramel);">
                                <i class="bi bi-cup-hot fs-3"></i>
                            </span>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">{{ $category->name }}</h6>
                        <small class="text-muted">{{ $category->products_count }} Menu Kopi</small>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</section>

<!-- Featured Products Section -->
<section class="container mb-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
        <div>
            <span class="text-caramel fw-bold text-uppercase small">Menu Terfavorit</span>
            <h2 class="fw-bold font-serif text-dark mb-0">Produk Unggulan Kami</h2>
        </div>
        <a href="{{ route('catalog.index') }}" class="btn btn-outline-caramel mt-3 mt-md-0">
            Lihat Semua Menu <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="row g-4">
        @forelse($featuredProducts as $product)
            <x-product-card :product="$product" />
        @empty
            <div class="col-12">
                <x-empty-state
                    title="Menu Belum Tersedia"
                    message="Menu sedang disiapkan oleh barista kami."
                />
            </div>
        @endforelse
    </div>
</section>

<!-- Why Choose Us / Keunggulan -->
<section class="py-5 mb-5" style="background-color: #f7efe5;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-caramel fw-bold text-uppercase small">Keunggulan Kami</span>
            <h2 class="fw-bold font-serif text-dark">Mengapa Memilih Rasa Kopi?</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border-0 rounded-4 shadow-sm text-center bg-white">
                    <div class="mb-3 text-caramel fs-1">
                        <i class="bi bi-award"></i>
                    </div>
                    <h5 class="fw-bold font-serif text-dark mb-2">Biji Kopi Pilihan</h5>
                    <p class="text-muted small mb-0">Hanya biji kopi arabika &amp; robusta grade satu yang dipanen dari petani lereng nusantara.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border-0 rounded-4 shadow-sm text-center bg-white">
                    <div class="mb-3 text-caramel fs-1">
                        <i class="bi bi-fire"></i>
                    </div>
                    <h5 class="fw-bold font-serif text-dark mb-2">Fresh Roasting</h5>
                    <p class="text-muted small mb-0">Disangrai secara berkala untuk mempertahankan profil rasa, aroma, dan keaslian karakter kopi.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border-0 rounded-4 shadow-sm text-center bg-white">
                    <div class="mb-3 text-caramel fs-1">
                        <i class="bi bi-person-hearts"></i>
                    </div>
                    <h5 class="fw-bold font-serif text-dark mb-2">Barista Bersertifikat</h5>
                    <p class="text-muted small mb-0">Setiap cangkir kopi diseduh dengan teknik presisi oleh tim barista yang berdedikasi tinggi.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border-0 rounded-4 shadow-sm text-center bg-white">
                    <div class="mb-3 text-caramel fs-1">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h5 class="fw-bold font-serif text-dark mb-2">Ambil / Antar Kurir</h5>
                    <p class="text-muted small mb-0">Pilih opsi ambil langsung di gerai tanpa antre atau diantar langsung ke depan pintu Anda.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="container mb-5">
    <div class="text-center mb-5">
        <span class="text-caramel fw-bold text-uppercase small">Ulasan Pelanggan</span>
        <h2 class="fw-bold font-serif text-dark">Apa Kata Pecinta Rasa Kopi?</h2>
    </div>

    <div class="row g-4">
        @forelse($testimonials as $testi)
            <div class="col-md-6 col-lg-3">
                <div class="card card-coffee h-100 p-4 border-0 shadow-sm d-flex flex-column">
                    <div class="star-rating mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= $testi->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </div>
                    <p class="card-text text-muted small fst-italic flex-grow-1 mb-3">
                        "{{ $testi->comment }}"
                    </p>
                    <div class="d-flex align-items-center gap-2 border-top pt-2">
                        <div class="bg-caramel text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                            {{ strtoupper(substr($testi->user->name ?? 'P', 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold small text-dark">{{ $testi->user->name ?? 'Pelanggan Setia' }}</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">Menu: {{ $testi->product->name ?? 'Kopi' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-md-6 col-lg-3">
                <div class="card card-coffee h-100 p-4 border-0 shadow-sm">
                    <div class="star-rating mb-2">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="card-text text-muted small fst-italic mb-3">"Kopi Susu Gula Aren-nya pas banget manisnya dan kopinya tetap terasa pekat dan wangi!"</p>
                    <h6 class="mb-0 fw-bold small text-dark">Andi Pratama</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Penikmat Kopi Jakarta</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card card-coffee h-100 p-4 border-0 shadow-sm">
                    <div class="star-rating mb-2">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="card-text text-muted small fst-italic mb-3">"Manual brew V60 biji kopinya fresh banget, notes floral dan fruity-nya keluar jelas."</p>
                    <h6 class="mb-0 fw-bold small text-dark">Siti Rahma</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Home Brewer</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card card-coffee h-100 p-4 border-0 shadow-sm">
                    <div class="star-rating mb-2">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="card-text text-muted small fst-italic mb-3">"Pesan lewat website gampang banget, pick up di toko tinggal tunjukin nomor invoice tanpa antre."</p>
                    <h6 class="mb-0 fw-bold small text-dark">Dimas Aditya</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Pelanggan Pick-up</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card card-coffee h-100 p-4 border-0 shadow-sm">
                    <div class="star-rating mb-2">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="card-text text-muted small fst-italic mb-3">"Packaging aman dan rapi, es latte-nya sampai ke kantor masih dingin dan segar!"</p>
                    <h6 class="mb-0 fw-bold small text-dark">Nadia Utami</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Pelanggan Delivery</small>
                </div>
            </div>
        @endforelse
    </div>
</section>

<!-- Coffee Shop Information & Location -->
<section class="container mb-5">
    <div class="card rounded-4 border-0 shadow-sm overflow-hidden" style="background-color: #ffffff;">
        <div class="row g-0">
            <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-center">
                <span class="text-caramel fw-bold text-uppercase small mb-1">Kunjungi Kedai Kami</span>
                <h3 class="fw-bold font-serif text-dark mb-3">Suasana Hangat Untuk Temu &amp; Kerja</h3>
                <p class="text-muted small mb-4">
                    Kedai kami menyediakan ruang indoor ber-AC dengan Wi-Fi kencang serta colokan di setiap meja untuk kenyamanan kerja (WFC), dan area outdoor rindang untuk bersantai bersama sahabat.
                </p>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-cream p-2 rounded text-caramel fs-5">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold small text-dark">Alamat Gerai:</div>
                            <div class="text-muted small">{{ $storeSettings['address'] }}</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-cream p-2 rounded text-caramel fs-5">
                            <i class="bi bi-clock-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold small text-dark">Jam Operasional:</div>
                            <div class="text-muted small">{{ $storeSettings['hours'] }}</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-cream p-2 rounded text-caramel fs-5">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div>
                            <div class="fw-bold small text-dark">Kontak &amp; Reservasi:</div>
                            <div class="text-muted small">{{ $storeSettings['phone'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <a href="https://wa.me/{{ $storeSettings['whatsapp'] }}" target="_blank" class="btn btn-caramel px-4 py-2">
                        <i class="bi bi-whatsapp me-1"></i> Chat Barista via WhatsApp
                    </a>
                </div>
            </div>

            <div class="col-lg-6 bg-light d-flex align-items-center justify-content-center" style="min-height: 340px; background: url('https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=800&q=80') center/cover no-repeat;">
            </div>
        </div>
    </div>
</section>

@endsection
