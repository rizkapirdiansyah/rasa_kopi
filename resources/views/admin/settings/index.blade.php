@extends('layouts.admin')

@section('page_title', 'Pengaturan Profil & Toko')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
            <h5 class="fw-bold text-dark mb-4 font-serif border-bottom pb-3">Informasi Umum &amp; Kontak Coffee Shop</h5>

            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nama Kedai Kopi <span class="text-danger">*</span></label>
                        <input type="text" name="store_name" class="form-control" value="{{ old('store_name', $settings['store_name']) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tagline Slogan <span class="text-danger">*</span></label>
                        <input type="text" name="store_tagline" class="form-control" value="{{ old('store_tagline', $settings['store_tagline']) }}" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">No. WhatsApp Pesanan <span class="text-danger">*</span></label>
                        <input type="text" name="store_whatsapp" class="form-control" value="{{ old('store_whatsapp', $settings['store_whatsapp']) }}" required placeholder="6281234567890">
                        <small class="text-muted" style="font-size: 0.75rem;">Gunakan format 62 (contoh: 6281234567890).</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Nomor Telepon Toko <span class="text-danger">*</span></label>
                        <input type="text" name="store_phone" class="form-control" value="{{ old('store_phone', $settings['store_phone']) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Email Bisnis <span class="text-danger">*</span></label>
                        <input type="email" name="store_email" class="form-control" value="{{ old('store_email', $settings['store_email']) }}" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold">Alamat Fisik Gerai / Toko <span class="text-danger">*</span></label>
                        <input type="text" name="store_address" class="form-control" value="{{ old('store_address', $settings['store_address']) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Tarif Ongkir Kurir Flat (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="shipping_flat_rate" class="form-control" value="{{ old('shipping_flat_rate', $settings['shipping_flat_rate']) }}" required min="0">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Jam Operasional <span class="text-danger">*</span></label>
                    <input type="text" name="store_hours" class="form-control" value="{{ old('store_hours', $settings['store_hours']) }}" required>
                </div>

                <h5 class="fw-bold text-dark mb-4 font-serif border-bottom pb-3 mt-4">Halaman Profil Perusahaan (Tentang Kami)</h5>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Sejarah &amp; Cerita Kopi</label>
                    <textarea name="about_story" rows="3" class="form-control">{{ old('about_story', $settings['about_story']) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Visi Perusahaan</label>
                    <textarea name="about_vision" rows="2" class="form-control">{{ old('about_vision', $settings['about_vision']) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Misi Perusahaan</label>
                    <textarea name="about_mission" rows="3" class="form-control">{{ old('about_mission', $settings['about_mission']) }}</textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-caramel px-4">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
