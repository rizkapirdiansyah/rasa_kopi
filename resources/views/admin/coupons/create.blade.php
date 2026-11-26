@extends('layouts.admin')

@section('page_title', 'Buat Kupon Promo Baru')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h5 class="fw-bold text-dark mb-0 font-serif">Formulir Kupon Diskon</h5>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kode Kupon (Huruf Kapital) <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase font-monospace" value="{{ old('code') }}" required placeholder="Contoh: NGOPIASIK">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nama Promo <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Diskon Akhir Pekan 20%">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Tipe Diskon <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" id="couponTypeSelect" required onchange="toggleCouponType(this.value)">
                            <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                            <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>Persentase (%)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold" id="couponValLabel">Nilai Diskon (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="value" class="form-control" value="{{ old('value') }}" required min="0" placeholder="10000">
                    </div>

                    <div class="col-md-4" id="maxDiscountCol">
                        <label class="form-label small fw-bold">Maksimal Diskon (Rp)</label>
                        <input type="number" name="max_discount" class="form-control" value="{{ old('max_discount') }}" placeholder="Opsional">
                        <small class="text-muted" style="font-size: 0.75rem;">Hanya berlaku untuk tipe persen.</small>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Minimal Belanja (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', 0) }}" required min="0">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Batas Jumlah Penggunaan (Limit)</label>
                        <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit') }}" placeholder="Kosongkan jika tak terbatas">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tanggal Mulai Berlaku</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tanggal Berakhir</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="isActive">
                        Aktifkan kupon ini sekarang
                    </label>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-caramel px-4">Simpan Kupon</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleCouponType(val) {
        const label = document.getElementById('couponValLabel');
        if (val === 'percent') {
            label.innerHTML = 'Persentase Diskon (%) <span class="text-danger">*</span>';
        } else {
            label.innerHTML = 'Nilai Diskon (Rp) <span class="text-danger">*</span>';
        }
    }
</script>

@endsection
