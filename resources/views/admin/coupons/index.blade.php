@extends('layouts.admin')

@section('page_title', 'Manajemen Kupon & Promo Diskon')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <p class="text-muted small mb-0">Kelola voucher promo potongan harga (persen atau nominal) yang dapat digunakan pelanggan saat checkout.</p>
    </div>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-caramel btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-plus-lg"></i> Tambah Kupon Promo
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th scope="col" class="ps-4">Kode Kupon</th>
                    <th scope="col">Nama Promo</th>
                    <th scope="col">Nilai Diskon</th>
                    <th scope="col">Min. Belanja</th>
                    <th scope="col" class="text-center">Penggunaan</th>
                    <th scope="col">Periode Berlaku</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                    <tr>
                        <td class="ps-4 py-3">
                            <span class="badge bg-light text-dark border font-monospace fs-6 px-2 py-1">
                                {{ $coupon->code }}
                            </span>
                        </td>

                        <td>
                            <strong class="text-dark">{{ $coupon->name }}</strong>
                        </td>

                        <td>
                            @if($coupon->type === 'percent')
                                <span class="fw-bold text-success">{{ $coupon->value }}%</span>
                                @if($coupon->max_discount)
                                    <small class="text-muted d-block">(Maks. Rp {{ number_format($coupon->max_discount, 0, ',', '.') }})</small>
                                @endif
                            @else
                                <span class="fw-bold text-success">Rp {{ number_format($coupon->value, 0, ',', '.') }}</span>
                            @endif
                        </td>

                        <td class="small text-muted">
                            Rp {{ number_format($coupon->min_order_amount, 0, ',', '.') }}
                        </td>

                        <td class="text-center small">
                            <span class="fw-bold">{{ $coupon->used_count }}</span>
                            @if($coupon->usage_limit)
                                <span class="text-muted">/ {{ $coupon->usage_limit }}</span>
                            @else
                                <span class="text-muted">/ &infin;</span>
                            @endif
                        </td>

                        <td class="small text-muted">
                            @if($coupon->start_date && $coupon->end_date)
                                {{ $coupon->start_date->format('d/m/y') }} - {{ $coupon->end_date->format('d/m/y') }}
                            @else
                                Selalu Berlaku
                            @endif
                        </td>

                        <td>
                            @if($coupon->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>

                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-outline-primary btn-sm p-1 px-2" title="Edit Kupon">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus kupon ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm p-1 px-2" title="Hapus Kupon">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">Belum ada kupon promo yang dibuat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 border-top d-flex justify-content-center">
        {{ $coupons->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
