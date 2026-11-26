@extends('layouts.admin')

@section('page_title', 'Edit Kategori: ' . $category->name)

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h5 class="fw-bold text-dark mb-0 font-serif">Edit Kategori</h5>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Deskripsi Kategori</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-caramel px-4">Perbarui Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
