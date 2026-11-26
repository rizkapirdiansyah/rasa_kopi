@extends('layouts.admin')

@section('page_title', 'Manajemen Ulasan & Rating')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted small mb-0">Moderasi ulasan dan rating pelanggan terhadap menu kopi Rasa Kopi.</p>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-3 p-3 bg-white mb-4">
    <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua Status Moderasi</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Ditampilkan (Disetujui)</option>
                <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Disembunyikan</option>
            </select>
        </div>

        <div class="col-md-5">
            <select name="rating" class="form-select form-select-sm">
                <option value="">Semua Bintang Rating</option>
                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Bintang</option>
                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Bintang</option>
                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Bintang</option>
                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Bintang</option>
                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Bintang</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-caramel btn-sm w-100"><i class="bi bi-funnel"></i> Filter</button>
            @if(request()->hasAny(['status', 'rating']))
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>
</div>

<div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th scope="col" class="ps-4">Menu Kopi</th>
                    <th scope="col">Pelanggan</th>
                    <th scope="col" class="text-center">Rating</th>
                    <th scope="col" style="max-width: 350px;">Komentar Ulasan</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $rev)
                    <tr>
                        <td class="ps-4 py-3">
                            <strong class="text-dark d-block">{{ $rev->product->name ?? 'Produk Dihapus' }}</strong>
                            <small class="text-muted">{{ $rev->product->category->name ?? '' }}</small>
                        </td>

                        <td>
                            <div class="fw-bold small text-dark">{{ $rev->user->name ?? 'Anonim' }}</div>
                            <small class="text-muted">{{ $rev->created_at->format('d/m/Y H:i') }}</small>
                        </td>

                        <td class="text-center">
                            <div class="star-rating small">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $rev->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </div>
                            <small class="fw-bold text-muted">{{ $rev->rating }}/5</small>
                        </td>

                        <td class="small text-muted" style="max-width: 350px;">
                            "{{ $rev->comment }}"
                        </td>

                        <td>
                            @if($rev->is_approved)
                                <span class="badge bg-success">Tampil di Web</span>
                            @else
                                <span class="badge bg-secondary">Disembunyikan</span>
                            @endif
                        </td>

                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <form action="{{ route('admin.reviews.toggle', $rev->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary btn-sm p-1 px-2" title="{{ $rev->is_approved ? 'Sembunyikan' : 'Tampilkan' }}">
                                        <i class="bi {{ $rev->is_approved ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                        <span>{{ $rev->is_approved ? 'Hide' : 'Show' }}</span>
                                    </button>
                                </form>

                                <form action="{{ route('admin.reviews.destroy', $rev->id) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus ulasan ini secara permanen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm p-1 px-2" title="Hapus Ulasan">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada data ulasan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 border-top d-flex justify-content-center">
        {{ $reviews->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
