@extends('layouts.admin')

@section('page_title', 'Edit Metode Pembayaran: ' . $paymentMethod->name)

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h5 class="fw-bold text-dark mb-0 font-serif">Edit Rekening Pembayaran</h5>
                <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <form action="{{ route('admin.payment-methods.update', $paymentMethod->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Metode <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $paymentMethod->name) }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nomor Rekening</label>
                        <input type="text" name="account_number" class="form-control font-monospace" value="{{ old('account_number', $paymentMethod->account_number) }}" placeholder="Kosongkan jika Tunai/COD">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Atas Nama Rekening</label>
                        <input type="text" name="account_name" class="form-control" value="{{ old('account_name', $paymentMethod->account_name) }}" placeholder="Contoh: PT RASA KOPI NUSANTARA">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Petunjuk Pembayaran untuk Pelanggan</label>
                    <textarea name="instructions" rows="3" class="form-control">{{ old('instructions', $paymentMethod->instructions) }}</textarea>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="isActive">
                        Tampilkan metode pembayaran ini di halaman checkout pelanggan
                    </label>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-caramel px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
