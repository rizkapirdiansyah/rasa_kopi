@extends('layouts.admin')

@section('page_title', 'Manajemen Kategori Menu')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <p class="text-muted small mb-0">Kelola kelompok menu kopi (seperti Espresso Based, Manual Brew, Non-Coffee, Signature).</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-caramel btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-plus-lg"></i> Tambah Kategori Baru
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th scope="col" class="ps-4">Nama Kategori</th>
                    <th scope="col">Slug URL</th>
                    <th scope="col">Deskripsi</th>
                    <th scope="col" class="text-center">Jumlah Produk</th>
                    <th scope="col" class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td class="ps-4 py-3">
                            <strong class="text-dark">{{ $category->name }}</strong>
                        </td>
                        <td class="font-monospace small text-muted">
                            {{ $category->slug }}
                        </td>
                        <td class="small text-muted">
                            {{ $category->description ?? '-' }}
                        </td>
                        <td class="text-center">
                            <span class="badge badge-cream text-dark">{{ $category->products_count }} Menu</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-outline-primary btn-sm p-1 px-2" title="Edit Kategori">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $category->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm p-1 px-2" title="Hapus Kategori">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada kategori yang dibuat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
