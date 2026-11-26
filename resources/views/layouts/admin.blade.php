<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - RASA KOPI')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Theme -->
    <link rel="stylesheet" href="{{ asset('css/coffee-theme.css') }}">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .sidebar-sticky {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .admin-topbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2d9cf;
        }
    </style>
    @stack('styles')
</head>
<body style="background-color: #f7f3ee;">

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block admin-sidebar collapse p-0 shadow-sm" id="sidebarMenu">
            <div class="sidebar-sticky d-flex flex-column p-3">
                <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center mb-4 text-decoration-none px-2 text-white">
                    <span class="fs-4 fw-bold font-serif">RASA<span class="text-caramel">KOPI</span></span>
                    <span class="badge bg-warning text-dark ms-2 small">ADMIN</span>
                </a>

                @php
                    $pendingOrdersCount = \App\Models\Order::whereIn('status', ['menunggu_pembayaran', 'dibayar'])->count();
                    $lowStockCount = \App\Models\Product::where('stock', '<=', 10)->count();
                @endphp

                <ul class="nav flex-column mb-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                            <i class="bi bi-bag-check me-2"></i> Pesanan
                            @if($pendingOrdersCount > 0)
                                <span class="badge bg-danger rounded-pill float-end">{{ $pendingOrdersCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                            <i class="bi bi-cup-hot me-2"></i> Produk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                            <i class="bi bi-tags me-2"></i> Kategori
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}" href="{{ route('admin.stock.index') }}">
                            <i class="bi bi-boxes me-2"></i> Stok &amp; Inventori
                            @if($lowStockCount > 0)
                                <span class="badge bg-warning text-dark rounded-pill float-end">{{ $lowStockCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                            <i class="bi bi-people me-2"></i> Pelanggan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">
                            <i class="bi bi-ticket-perforated me-2"></i> Promo &amp; Kupon
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">
                            <i class="bi bi-star-half me-2"></i> Review &amp; Rating
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}" href="{{ route('admin.payment-methods.index') }}">
                            <i class="bi bi-credit-card me-2"></i> Metode Pembayaran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                            <i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Penjualan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                            <i class="bi bi-gear me-2"></i> Pengaturan Toko
                        </a>
                    </li>
                </ul>

                <hr class="text-white opacity-25 my-3">

                <div class="px-2">
                    <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-light btn-sm w-100 mb-2 d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-box-arrow-up-right"></i> Lihat Website
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                            <i class="bi bi-power"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-3">
            <!-- Top Navbar for Mobile Toggle & Admin Info -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-dark d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <h1 class="h4 fw-bold m-0 text-dark">@yield('page_title', 'Dashboard')</h1>
                </div>

                <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center gap-3">
                    <span class="text-muted small d-none d-lg-inline">
                        <i class="bi bi-calendar-event me-1"></i> {{ date('l, d F Y') }}
                    </span>
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-white border rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="bi bi-person-fill text-caramel fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold small text-dark">{{ auth()->user()->name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Administrator</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flash Alerts -->
            <x-alert />

            <!-- Page Body -->
            @yield('content')
        </main>
    </div>
</div>

<!-- Bootstrap 5.3.3 Bundle with Popper JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

@stack('scripts')
</body>
</html>
