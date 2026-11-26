@extends('layouts.app')

@section('title', $product->name . ' - RASA KOPI')

@section('content')

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}" class="text-decoration-none text-muted">Menu</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalog.index', ['category' => $product->category->slug]) }}" class="text-decoration-none text-muted">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <!-- Product Detail Card -->
    <div class="card card-coffee border-0 shadow-sm p-4 p-lg-5 mb-5">
        <div class="row g-5 align-items-center">
            <!-- Product Image -->
            <div class="col-lg-5">
                <div class="rounded-4 overflow-hidden shadow-sm" style="background-color: #f7efe5; height: 380px; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-fluid w-100 h-100" style="object-fit: cover;">
                </div>
            </div>

            <!-- Product Info & Actions -->
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge badge-cream text-dark">
                        {{ $product->category->name }}
                    </span>
                    @if($product->stock <= 0)
                        <span class="badge bg-danger">Stok Habis</span>
                    @elseif($product->isLowStock())
                        <span class="badge bg-warning text-dark">Sisa {{ $product->stock }} Cup</span>
                    @else
                        <span class="badge bg-success">Tersedia ({{ $product->stock }} unit)</span>
                    @endif
                </div>

                <h1 class="display-6 fw-bold font-serif text-dark mb-2">{{ $product->name }}</h1>

                <!-- Rating -->
                <div class="d-flex align-items-center gap-2 mb-3 star-rating">
                    <div>
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= round($product->average_rating) ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </div>
                    <span class="fw-bold">{{ $product->average_rating }}</span>
                    <span class="text-muted small">({{ $product->reviews_count }} ulasan pelanggan)</span>
                </div>

                <!-- Price -->
                <div class="mb-4">
                    <span class="display-6 fw-bold text-caramel">{{ $product->formatted_price }}</span>
                </div>

                <!-- Description -->
                <div class="mb-4 text-muted leading-relaxed">
                    <h6 class="fw-bold text-dark mb-2">Deskripsi Racikan:</h6>
                    <p class="mb-0">{{ $product->description ?? 'Racikan kopi istimewa khas Rasa Kopi yang diseduh dari biji kopi pilihan nusantara.' }}</p>
                </div>

                <!-- Add to Cart Form -->
                @if($product->stock > 0)
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-auto">
                                <label for="qtyInput" class="form-label mb-0 small fw-bold">Jumlah:</label>
                            </div>
                            <div class="col-auto" style="width: 140px;">
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" onclick="decrementQty()">-</button>
                                    <input type="number" id="qtyInput" name="quantity" class="form-control text-center" value="1" min="1" max="{{ $product->stock }}">
                                    <button class="btn btn-outline-secondary" type="button" onclick="incrementQty()">+</button>
                                </div>
                            </div>
                            <div class="col-auto text-muted small">
                                Maksimal: {{ $product->stock }}
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3">
                            <button type="submit" name="action" value="add" class="btn btn-caramel btn-lg px-4 d-flex align-items-center gap-2">
                                <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                            </button>
                            <button type="submit" name="action" value="buy_now" class="btn btn-coffee-dark btn-lg px-4 d-flex align-items-center gap-2">
                                <i class="bi bi-lightning-charge-fill"></i> Beli Sekarang
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-secondary py-3 mb-4">
                        <i class="bi bi-exclamation-circle me-1"></i> Maaf, saat ini stok kopi ini sedang habis. Silakan pilih menu lainnya atau hubungi barista kami.
                    </div>
                @endif

                <!-- Auxiliary Buttons: Wishlist & WhatsApp -->
                <div class="d-flex flex-wrap align-items-center gap-3 pt-3 border-top">
                    @auth
                        @php
                            $inWishlist = auth()->user()->wishlists()->where('product_id', $product->id)->exists();
                        @endphp
                        <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                                <i class="bi {{ $inWishlist ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                                <span>{{ $inWishlist ? 'Hapus dari Wishlist' : 'Simpan ke Wishlist' }}</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                            <i class="bi bi-heart"></i> Simpan ke Wishlist
                        </a>
                    @endauth

                    @php
                        $storeWhatsApp = \App\Models\StoreSetting::get('store_whatsapp', '6281234567890');
                        $waMsg = urlencode("Halo Rasa Kopi, saya tertarik dengan menu {$product->name} seharga {$product->formatted_price}. Apakah bisa dipesan sekarang?");
                    @endphp
                    <a href="https://wa.me/{{ $storeWhatsApp }}?text={{ $waMsg }}" target="_blank" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1">
                        <i class="bi bi-whatsapp"></i> Tanya Barista via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Reviews & Rating Section -->
    <div class="card card-coffee border-0 shadow-sm p-4 p-lg-5 mb-5">
        <div class="row g-4">
            <div class="col-lg-4 border-end-lg">
                <h4 class="fw-bold font-serif text-dark mb-3">Ulasan &amp; Rating</h4>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <span class="display-4 fw-bold text-dark font-serif">{{ $product->average_rating }}</span>
                    <span class="text-muted fs-5">/ 5.0</span>
                </div>
                <div class="star-rating fs-5 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi {{ $i <= round($product->average_rating) ? 'bi-star-fill' : 'bi-star' }}"></i>
                    @endfor
                </div>
                <p class="text-muted small">Berdasarkan {{ $product->reviews_count }} ulasan pelanggan yang telah terverifikasi memesan menu ini.</p>

                <!-- Form write review if eligible -->
                @if($userCanReview)
                    <div class="mt-4 p-3 rounded-3" style="background-color: #f7efe5;">
                        <h6 class="fw-bold text-dark mb-2">Tulis Ulasan Anda</h6>
                        <form action="{{ route('reviews.store', $product->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Rating Bintang:</label>
                                <select name="rating" class="form-select form-select-sm" required>
                                    <option value="5">★★★★★ (5 Bintang - Luar Biasa)</option>
                                    <option value="4">★★★★☆ (4 Bintang - Enak Sekali)</option>
                                    <option value="3">★★★☆☆ (3 Bintang - Cukup Baik)</option>
                                    <option value="2">★★☆☆☆ (2 Bintang - Kurang Pas)</option>
                                    <option value="1">★☆☆☆☆ (1 Bintang - Mengecewakan)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Ulasan Anda:</label>
                                <textarea name="comment" rows="3" class="form-control form-control-sm" placeholder="Bagikan pengalaman rasa kopi ini..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-caramel btn-sm w-100">Kirim Ulasan</button>
                        </form>
                    </div>
                @else
                    <div class="alert alert-light border small text-muted mt-3">
                        <i class="bi bi-info-circle me-1"></i> Anda dapat memberikan ulasan setelah menyelesaikan pesanan menu ini.
                    </div>
                @endif
            </div>

            <!-- Review Items List -->
            <div class="col-lg-8">
                <h5 class="fw-bold font-serif text-dark mb-3">Ulasan Terbaru</h5>

                @forelse($product->approvedReviews as $review)
                    <div class="border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-caramel text-white rounded-circle d-flex align-items-center justify-content-center fw-bold small" style="width: 32px; height: 32px;">
                                    {{ strtoupper(substr($review->user->name ?? 'P', 0, 1)) }}
                                </div>
                                <span class="fw-bold text-dark">{{ $review->user->name ?? 'Pelanggan Rasa Kopi' }}</span>
                                <span class="badge bg-light text-success border border-success small"><i class="bi bi-patch-check"></i> Pembeli Terverifikasi</span>
                            </div>
                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="star-rating small mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </div>
                        <p class="text-muted small mb-0">{{ $review->comment }}</p>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-chat-heart fs-1 d-block mb-2 text-caramel opacity-50"></i>
                        Belum ada ulasan untuk menu ini. Jadilah yang pertama memberikan ulasan!
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="mb-5">
            <h3 class="fw-bold font-serif text-dark mb-4">Rekomendasi Menu Terkait</h3>
            <div class="row g-4">
                @foreach($relatedProducts as $related)
                    <x-product-card :product="$related" />
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
    function incrementQty() {
        const input = document.getElementById('qtyInput');
        const max = parseInt(input.getAttribute('max')) || 999;
        let val = parseInt(input.value) || 1;
        if (val < max) {
            input.value = val + 1;
        }
    }

    function decrementQty() {
        const input = document.getElementById('qtyInput');
        let val = parseInt(input.value) || 1;
        if (val > 1) {
            input.value = val - 1;
        }
    }
</script>

@endsection
