<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RASA KOPI - Coffee Shop & Roastery Nusantara')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Coffee Theme -->
    <link rel="stylesheet" href="{{ asset('css/coffee-theme.css') }}">

    @stack('styles')
</head>
<body>

    <!-- Top Announcement Bar -->
    <div class="py-2 text-center text-white small" style="background-color: #1a0d07; font-weight: 500;">
        <div class="container d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clock me-1"></i> Buka Setiap Hari: 08:00 - 22:00 WIB</span>
            <span class="d-none d-md-inline">Nikmati Promo Diskon dengan kupon: <strong>NGOPIHEMAT</strong></span>
            <span><i class="bi bi-geo-alt me-1"></i> Senopati, Jakarta Selatan</span>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-coffee sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <span class="fs-3 fw-bold text-dark font-serif tracking-wide">
                    RASA<span class="text-caramel">KOPI</span>
                </span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <!-- Nav Links -->
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active fw-bold' : '' }}" href="{{ route('home') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('catalog.*') ? 'active fw-bold' : '' }}" href="{{ route('catalog.index') }}">Menu &amp; Katalog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active fw-bold' : '' }}" href="{{ route('about') }}">Tentang Kami</a>
                    </li>
                </ul>

                <!-- Right Actions: Search, Wishlist, Cart, User -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Wishlist -->
                    @auth
                        @php
                            $wishlistCount = auth()->user()->wishlists()->count();
                        @endphp
                        <a href="{{ route('wishlist.index') }}" class="btn btn-outline-secondary position-relative rounded-circle p-2" title="Wishlist">
                            <i class="bi bi-heart fs-5"></i>
                            @if($wishlistCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $wishlistCount }}
                                </span>
                            @endif
                        </a>
                    @endauth

                    <!-- Cart -->
                    @php
                        $cartCount = app(\App\Services\CartService::class)->getItemCount();
                    @endphp
                    <a href="{{ route('cart.index') }}" class="btn btn-caramel position-relative rounded-pill px-3 py-2 d-flex align-items-center gap-2" title="Keranjang Belanja">
                        <i class="bi bi-cart3 fs-5"></i>
                        <span class="fw-bold">{{ $cartCount }}</span>
                    </a>

                    <!-- User Account Dropdown -->
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-dark dropdown-toggle rounded-pill px-3 py-2 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle fs-5"></i>
                                <span class="d-none d-sm-inline">{{ Str::limit(auth()->user()->name, 12) }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                                    <small class="text-muted">{{ auth()->user()->email }}</small>
                                    @if(auth()->user()->isAdmin())
                                        <div class="mt-1"><span class="badge bg-warning text-dark">Administrator</span></div>
                                    @endif
                                </li>
                                @if(auth()->user()->isAdmin())
                                    <li>
                                        <a class="dropdown-item py-2 fw-semibold text-danger" href="{{ route('admin.dashboard') }}">
                                            <i class="bi bi-speedometer2 me-2"></i> Dashboard Admin
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('profile.show') }}">
                                        <i class="bi bi-person me-2"></i> Profil &amp; Alamat
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('orders.index') }}">
                                        <i class="bi bi-bag-check me-2"></i> Pesanan Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('wishlist.index') }}">
                                        <i class="bi bi-heart me-2"></i> Wishlist Favorit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <div class="d-flex gap-2">
                            <a href="{{ route('login') }}" class="btn btn-outline-caramel rounded-pill px-3 py-2">Masuk</a>
                            <a href="{{ route('register') }}" class="btn btn-caramel rounded-pill px-3 py-2 d-none d-sm-inline-block">Daftar</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Global Alerts / Flash Messages -->
    <div class="container mt-3">
        <x-alert />
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Floating WhatsApp Contact Button -->
    @php
        $globalWhatsApp = \App\Models\StoreSetting::get('store_whatsapp', '6281234567890');
        $globalWAMsg = urlencode('Halo Rasa Kopi, saya ingin bertanya tentang menu dan pemesanan kopi.');
    @endphp
    <a href="https://wa.me/{{ $globalWhatsApp }}?text={{ $globalWAMsg }}" target="_blank" class="whatsapp-float" title="Hubungi Kami via WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <!-- Footer -->
    <footer class="footer-coffee mt-5">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4 col-md-6">
                    <h4 class="font-serif fw-bold text-white mb-3">
                        RASA<span class="text-caramel">KOPI</span>
                    </h4>
                    <p class="text-muted small pe-lg-4">
                        Menyajikan perpaduan cita rasa kopi terbaik nusantara yang dipetik dari petani lokal pilihan dan diseduh oleh barista berpengalaman untuk menemani setiap momen berharga Anda.
                    </p>
                    <div class="d-flex gap-3 fs-5 mt-3">
                        <a href="#" class="text-white"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-twitter-x"></i></a>
                        <a href="https://wa.me/{{ $globalWhatsApp }}" target="_blank" class="text-white"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Menu Cepat</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        <li><a href="{{ route('home') }}">Beranda</a></li>
                        <li><a href="{{ route('catalog.index') }}">Semua Menu Kopi</a></li>
                        <li><a href="{{ route('about') }}">Tentang Kami</a></li>
                        <li><a href="{{ route('cart.index') }}">Keranjang Belanja</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Layanan &amp; Bantuan</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        <li><a href="{{ route('orders.index') }}">Status &amp; Riwayat Pesanan</a></li>
                        <li><a href="{{ route('profile.show') }}">Profil Pelanggan</a></li>
                        <li><a href="{{ route('wishlist.index') }}">Wishlist Favorit</a></li>
                        <li><a href="https://wa.me/{{ $globalWhatsApp }}" target="_blank">Layanan Pelanggan WhatsApp</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Lokasi Kedai</h6>
                    <p class="small text-muted mb-2">
                        <i class="bi bi-geo-alt text-caramel me-2"></i>
                        {{ \App\Models\StoreSetting::get('store_address', 'Jl. Kopi Kenikmatan No. 18, Senopati, Jakarta Selatan') }}
                    </p>
                    <p class="small text-muted mb-2">
                        <i class="bi bi-telephone text-caramel me-2"></i>
                        {{ \App\Models\StoreSetting::get('store_phone', '0812-3456-7890') }}
                    </p>
                    <p class="small text-muted">
                        <i class="bi bi-clock text-caramel me-2"></i>
                        {{ \App\Models\StoreSetting::get('store_hours', 'Senin - Minggu: 08:00 - 22:00 WIB') }}
                    </p>
                </div>
            </div>

            <div class="pt-4 border-top border-secondary border-opacity-25 d-flex flex-column flex-md-row justify-content-between align-items-center small text-muted">
                <div>&copy; {{ date('Y') }} RASA KOPI. Seluruh hak cipta dilindungi.</div>
                <div class="mt-2 mt-md-0">
                    <span class="badge bg-dark me-2">Transfer Bank</span>
                    <span class="badge bg-dark me-2">BCA</span>
                    <span class="badge bg-dark me-2">Mandiri</span>
                    <span class="badge bg-dark">Tunai (COD)</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 Bundle with Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    @stack('scripts')
</body>
</html>
