@extends('layouts.admin')

@section('page_title', 'Manajemen Stok & Inventori')

@section('content')

<!-- Stock Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center">
            <span class="text-muted small fw-bold text-uppercase">Total Item Menu</span>
            <h3 class="fw-bold text-dark font-serif mb-0">{{ $stats['total'] }}</h3>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center">
            <span class="text-success small fw-bold text-uppercase">Stok Aman (> 10)</span>
            <h3 class="fw-bold text-success font-serif mb-0">{{ $stats['safe'] }}</h3>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center">
            <span class="text-warning small fw-bold text-uppercase">Stok Menipis (&le; 10)</span>
            <h3 class="fw-bold text-warning font-serif mb-0">{{ $stats['low'] }}</h3>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center">
            <span class="text-danger small fw-bold text-uppercase">Stok Habis (0)</span>
            <h3 class="fw-bold text-danger font-serif mb-0">{{ $stats['out'] }}</h3>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-3 p-3 bg-white mb-4">
    <form action="{{ route('admin.stock.index') }}" method="GET" class="row g-2 align-items-center">
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
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua Status Stok</option>
                <option value="safe" {{ request('status') === 'safe' ? 'selected' : '' }}>Stok Aman</option>
                <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Stok Menipis</option>
                <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>Stok Habis</option>
            </select>
        </div>

        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-caramel btn-sm w-100"><i class="bi bi-funnel"></i></button>
            @if(request()->hasAny(['q', 'category_id', 'status']))
                <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Stock Table with Quick Adjustment Form -->
<div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th class="ps-4">Menu Kopi</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th class="text-center">Sisa Stok</th>
                    <th>Status</th>
                    <th class="pe-4 text-end" style="min-width: 280px;">Penyesuaian Cepat Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $product->image_url }}" alt="" class="rounded border" style="width: 44px; height: 44px; object-fit: cover;">
                                <div>
                                    <strong class="text-dark d-block">{{ $product->name }}</strong>
                                    <small class="text-muted font-monospace">/{{ $product->slug }}</small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge badge-cream text-dark">{{ $product->category->name ?? '-' }}</span>
                        </td>

                        <td class="small text-muted">{{ $product->formatted_price }}</td>

                        <td class="text-center">
                            <span class="fs-6 fw-bold {{ $product->stock <= 0 ? 'text-danger' : ($product->isLowStock() ? 'text-warning' : 'text-success') }}">
                                {{ $product->stock }}
                            </span>
                        </td>

                        <td>
                            @if($product->stock <= 0)
                                <span class="badge bg-danger">Habis</span>
                            @elseif($product->isLowStock())
                                <span class="badge bg-warning text-dark">Menipis</span>
                            @else
                                <span class="badge bg-success">Aman</span>
                            @endif
                        </td>

                        <td class="pe-4 text-end">
                            <form action="{{ route('admin.stock.update', $product->id) }}" method="POST" class="d-inline-flex gap-1 align-items-center justify-content-end m-0">
                                @csrf
                                <select name="action_type" class="form-select form-select-sm" style="width: 100px;">
                                    <option value="add">+ Tambah</option>
                                    <option value="set">= Set Baru</option>
                                </select>
                                <input type="number" name="stock_value" class="form-control form-control-sm text-center" style="width: 70px;" min="0" required placeholder="Jml">
                                <button type="submit" class="btn btn-caramel btn-sm py-1 px-2" title="Simpan Stok">
                                    <i class="bi bi-check2"></i> Simpan
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Tidak ada data produk yang cocok.</td>
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
