@extends('layouts.admin')

@section('page_title', 'Edit Produk: ' . $product->name)

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h5 class="fw-bold text-dark mb-0 font-serif">Edit Menu Kopi</h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>

            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kategori Menu <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" value="{{ old('price', (int)$product->price) }}" required min="0" step="500">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Jumlah Stok <span class="text-danger">*</span></label>
                        <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required min="0">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Ganti Foto Produk (Opsional)</label>
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <img src="{{ $product->image_url }}" alt="Preview" class="rounded border" style="width: 70px; height: 70px; object-fit: cover;">
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengganti foto saat ini.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Deskripsi Menu &amp; Profil Rasa</label>
                    <textarea name="description" rows="4" class="form-control">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-caramel px-4">Perbarui Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
