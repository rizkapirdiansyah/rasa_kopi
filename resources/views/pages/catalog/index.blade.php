@extends('layouts.app')

@section('title', 'Katalog Menu Kopi - RASA KOPI')

@section('content')

<!-- Header Banner -->
<section class="py-4 text-center text-white mb-4" style="background: linear-gradient(135deg, #2c1810 0%, #3e2723 100%);">
    <div class="container py-3">
        <h1 class="display-5 fw-bold font-serif mb-2">Katalog Menu Kopi</h1>
        <p class="text-light opacity-90 mx-auto mb-0" style="max-width: 500px;">
            Pilih racikan kopi terbaik untuk menemani aktivitas harian Anda.
        </p>
    </div>
</section>

<div class="container mb-5">
    <!-- Filter & Search Bar -->
    <div class="card card-coffee border-0 shadow-sm p-4 mb-4">
        <form action="{{ route('catalog.index') }}" method="GET" class="row g-3 align-items-center">
            <!-- Search Keyword -->
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="Cari nama kopi...">
                </div>
            </div>

            <!-- Category Filter -->
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->products_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Stock Status -->
            <div class="col-md-2">
                <select name="stock_status" class="form-select">
                    <option value="">Semua Stok</option>
                    <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>Tersedia</option>
                    <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>

            <!-- Sort By -->
            <div class="col-md-2">
                <select name="sort" class="form-select">
                    <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Harga: Termurah</option>
                    <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Harga: Termahal</option>
                    <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Nama: A - Z</option>
                    <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Nama: Z - A</option>
                </select>
            </div>

            <!-- Submit & Reset Buttons -->
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-caramel w-100" title="Terapkan Filter">
                    <i class="bi bi-funnel"></i>
                </button>
                @if(request()->hasAny(['q', 'category', 'stock_status', 'sort']))
                    <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Active Filter Tags Display -->
    @if(request()->hasAny(['q', 'category', 'stock_status']))
        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
            <span class="text-muted small">Filter aktif:</span>
            @if(request('q'))
                <span class="badge bg-light text-dark border">Pencarian: "{{ request('q') }}"</span>
            @endif
            @if(request('category'))
                <span class="badge bg-light text-dark border">Kategori: {{ request('category') }}</span>
            @endif
            @if(request('stock_status'))
                <span class="badge bg-light text-dark border">Stok: {{ request('stock_status') === 'in_stock' ? 'Tersedia' : 'Habis' }}</span>
            @endif
            <a href="{{ route('catalog.index') }}" class="small text-danger text-decoration-none ms-2">Hapus Semua Filter</a>
        </div>
    @endif

    <!-- Product Grid -->
    <div class="row g-4">
        @forelse($products as $product)
            <x-product-card :product="$product" />
        @empty
            <div class="col-12">
                <x-empty-state
                    icon="bi-search"
                    title="Menu Kopi Tidak Ditemukan"
                    message="Tidak ada produk kopi yang sesuai dengan kriteria pencarian atau filter Anda."
                    actionText="Lihat Semua Menu"
                    :actionUrl="route('catalog.index')"
                />
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-5">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
