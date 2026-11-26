@extends('layouts.admin')

@section('page_title', 'Manajemen Produk Menu')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <p class="text-muted small mb-0">Kelola seluruh menu kopi dan minuman yang ditampilkan di katalog website.</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-caramel btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-plus-lg"></i> Tambah Produk Baru
    </a>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-3 p-3 bg-white mb-4">
    <form action="{{ route('admin.products.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama produk...">
            </div>
        </div>

        <div class="col-md-3">
            <select name="category_id" class="form-select form-select-sm">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select name="stock_status" class="form-select form-select-sm">
                <option value="">Semua Kondisi Stok</option>
                <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Stok Menipis (&le; 10)</option>
                <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Stok Habis (0)</option>
            </select>
        </div>

        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-caramel btn-sm w-100" title="Filter"><i class="bi bi-funnel"></i></button>
            @if(request()->hasAny(['q', 'category_id', 'stock_status']))
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th scope="col" class="ps-4">Foto &amp; Menu</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Harga</th>
                    <th scope="col" class="text-center">Stok</th>
                    <th scope="col" class="text-center">Rating</th>
                    <th scope="col" class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded-3 shadow-sm border" style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark">{{ $product->name }}</div>
                                    <small class="text-muted font-monospace">/{{ $product->slug }}</small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge badge-cream text-dark">{{ $product->category->name ?? '-' }}</span>
                        </td>

                        <td class="fw-semibold text-dark">
                            {{ $product->formatted_price }}
                        </td>

                        <td class="text-center">
                            @if($product->stock <= 0)
                                <span class="badge bg-danger">Habis (0)</span>
                            @elseif($product->isLowStock())
                                <span class="badge bg-warning text-dark">Menipis ({{ $product->stock }})</span>
                            @else
                                <span class="badge bg-success">{{ $product->stock }}</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <span class="text-warning small"><i class="bi bi-star-fill"></i></span>
                            <span class="small fw-bold">{{ $product->average_rating }}</span>
                            <small class="text-muted">({{ $product->reviews_count }})</small>
                        </td>

                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('catalog.show', $product->slug) }}" target="_blank" class="btn btn-outline-info btn-sm p-1 px-2" title="Lihat di Web">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-outline-primary btn-sm p-1 px-2" title="Edit Produk">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ $product->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm p-1 px-2" title="Hapus Produk">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            Tidak ada produk yang cocok dengan pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 border-top d-flex justify-content-center">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
