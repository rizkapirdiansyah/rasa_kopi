@extends('layouts.admin')

@section('page_title', 'Tambah Produk Kopi Baru')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h5 class="fw-bold text-dark mb-0 font-serif">Formulir Tambah Menu Kopi</h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>

            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Caramel Macchiato">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kategori Menu <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" value="{{ old('price') }}" required min="0" step="500" placeholder="25000">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Stok Awal <span class="text-danger">*</span></label>
                        <input type="number" name="stock" class="form-control" value="{{ old('stock', 20) }}" required min="0" placeholder="20">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Foto Produk</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">Format yang didukung: JPG, PNG, WEBP (Maksimal 2MB).</small>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Deskripsi Menu &amp; Profil Rasa</label>
                    <textarea name="description" rows="4" class="form-control" placeholder="Jelaskan karakteristik kopi, komposisi susu, atau asal biji kopi...">{{ old('description') }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-caramel px-4">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
