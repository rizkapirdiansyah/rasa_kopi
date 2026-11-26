@props(['product'])

@php
    $inWishlist = false;
    if (auth()->check()) {
        $inWishlist = auth()->user()->wishlists()->where('product_id', $product->id)->exists();
    }
@endphp

<div class="col-sm-6 col-md-4 col-lg-3 mb-4">
    <div class="card card-coffee h-100 d-flex flex-column position-relative">
        <!-- Stock & Wishlist Badges -->
        <div class="position-absolute top-0 start-0 p-3 d-flex flex-column gap-1" style="z-index: 2;">
            <span class="badge badge-cream text-dark">
                {{ $product->category->name ?? 'Kopi' }}
            </span>
            @if($product->stock <= 0)
                <span class="badge bg-danger">Habis</span>
            @elseif($product->isLowStock())
                <span class="badge bg-warning text-dark">Sisa {{ $product->stock }}</span>
            @endif
        </div>

        <div class="position-absolute top-0 end-0 p-3" style="z-index: 2;">
            @auth
                <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm border-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="{{ $inWishlist ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}">
                        <i class="bi {{ $inWishlist ? 'bi-heart-fill text-danger' : 'bi-heart text-secondary' }}"></i>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-light rounded-circle shadow-sm border-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Login untuk simpan ke Wishlist">
                    <i class="bi bi-heart text-secondary"></i>
                </a>
            @endauth
        </div>

        <!-- Product Image -->
        <a href="{{ route('catalog.show', $product->slug) }}" class="text-decoration-none overflow-hidden" style="height: 210px; background-color: #f7efe5; display: flex; align-items: center; justify-content: center;">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-fluid w-100 h-100" style="object-fit: cover; transition: transform 0.3s ease;">
        </a>

        <!-- Card Body -->
        <div class="card-body d-flex flex-column p-3">
            <!-- Ratings -->
            <div class="d-flex align-items-center mb-1 star-rating small">
                @php
                    $rating = $product->average_rating;
                @endphp
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= round($rating))
                        <i class="bi bi-star-fill"></i>
                    @else
                        <i class="bi bi-star"></i>
                    @endif
                @endfor
                <span class="ms-1 text-muted">({{ $product->reviews_count }})</span>
            </div>

            <!-- Title -->
            <h6 class="card-title fw-bold mb-1">
                <a href="{{ route('catalog.show', $product->slug) }}" class="text-dark text-decoration-none">
                    {{ $product->name }}
                </a>
            </h6>

            <!-- Description summary -->
            <p class="card-text text-muted small flex-grow-1 mb-3">
                {{ Str::limit($product->description, 55, '...') }}
            </p>

            <!-- Price & Add to Cart -->
            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                <div>
                    <span class="d-block text-muted" style="font-size: 0.75rem;">Harga</span>
                    <span class="fw-bold text-caramel fs-5">{{ $product->formatted_price }}</span>
                </div>

                @if($product->stock > 0)
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-caramel btn-sm px-3 d-flex align-items-center gap-1" title="Tambah ke Keranjang">
                            <i class="bi bi-cart-plus"></i>
                            <span>Beli</span>
                        </button>
                    </form>
                @else
                    <button class="btn btn-secondary btn-sm px-3" disabled>Habis</button>
                @endif
            </div>
        </div>
    </div>
</div>
