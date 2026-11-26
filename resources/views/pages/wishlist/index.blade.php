@extends('layouts.app')

@section('title', 'Wishlist Favorit - RASA KOPI')

@section('content')

<div class="container py-4 mb-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold font-serif text-dark mb-0">Wishlist Menu Favorit</h1>
            <p class="text-muted small mb-0">Daftar kopi yang Anda sukai dan rencanakan untuk dinikmati.</p>
        </div>
        <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Jelajahi Menu Lain
        </a>
    </div>

    @if($wishlists->isNotEmpty())
        <div class="row g-4">
            @foreach($wishlists as $wish)
                @if($wish->product)
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card card-coffee h-100 d-flex flex-column position-relative">
                            <!-- Category Badge -->
                            <div class="position-absolute top-0 start-0 p-3" style="z-index: 2;">
                                <span class="badge badge-cream text-dark">
                                    {{ $wish->product->category->name ?? 'Kopi' }}
                                </span>
                            </div>

                            <!-- Remove from Wishlist button -->
                            <div class="position-absolute top-0 end-0 p-3" style="z-index: 2;">
                                <form action="{{ route('wishlist.toggle', $wish->product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm border-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Hapus dari Wishlist">
                                        <i class="bi bi-heart-fill text-danger"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Product Image -->
                            <a href="{{ route('catalog.show', $wish->product->slug) }}" class="overflow-hidden" style="height: 200px; background-color: #f7efe5; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ $wish->product->image_url }}" alt="{{ $wish->product->name }}" class="img-fluid w-100 h-100" style="object-fit: cover;">
                            </a>

                            <!-- Card Body -->
                            <div class="card-body d-flex flex-column p-3">
                                <h6 class="card-title fw-bold mb-1">
                                    <a href="{{ route('catalog.show', $wish->product->slug) }}" class="text-dark text-decoration-none">
                                        {{ $wish->product->name }}
                                    </a>
                                </h6>

                                <div class="mb-3">
                                    <span class="fw-bold text-caramel fs-5">{{ $wish->product->formatted_price }}</span>
                                </div>

                                <div class="mt-auto d-flex gap-2">
                                    @if($wish->product->stock > 0)
                                        <form action="{{ route('wishlist.move-to-cart', $wish->product->id) }}" method="POST" class="w-100 m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-caramel btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                                                <i class="bi bi-cart-plus"></i> Pindah ke Keranjang
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-secondary btn-sm w-100" disabled>Stok Habis</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $wishlists->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="card card-coffee border-0 shadow-sm p-5">
            <x-empty-state
                icon="bi-heart"
                title="Wishlist Anda Masih Kosong"
                message="Simpan menu kopi yang ingin Anda coba nanti dengan menekan tombol hati pada produk."
                actionText="Jelajahi Menu Kopi"
                :actionUrl="route('catalog.index')"
            />
        </div>
    @endif
</div>

@endsection
