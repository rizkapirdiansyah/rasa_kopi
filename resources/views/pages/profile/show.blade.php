@extends('layouts.app')

@section('title', 'Profil Saya - RASA KOPI')

@section('content')

<div class="container py-4 mb-5">
    <div class="mb-4">
        <h1 class="h3 fw-bold font-serif text-dark mb-0">Profil &amp; Alamat Pelanggan</h1>
        <p class="text-muted small">Kelola informasi akun Anda dan daftar alamat pengiriman untuk kemudahan checkout.</p>
    </div>

    <div class="row g-4">
        <!-- Left: Edit Profile & Password -->
        <div class="col-lg-6">
            <div class="card card-coffee border-0 shadow-sm p-4 mb-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-caramel text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 56px; height: 56px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">{{ $user->name }}</h5>
                        <small class="text-muted">{{ $user->email }}</small>
                        <div><span class="badge badge-cream text-dark">{{ ucfirst($user->role) }}</span></div>
                    </div>
                </div>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h6 class="fw-bold font-serif text-dark mb-3">Informasi Pribadi</h6>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly disabled>
                        <small class="text-muted" style="font-size: 0.75rem;">Email akun tidak dapat diubah.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Nomor WhatsApp / HP</label>
                        <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold font-serif text-dark mb-3">Ganti Kata Sandi (Opsional)</h6>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kata Sandi Saat Ini</label>
                        <input type="password" name="current_password" class="form-control" placeholder="Kosongkan jika tidak ingin mengganti password">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kata Sandi Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Konfirmasi Sandi Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi sandi baru">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-caramel px-4">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Saved Addresses -->
        <div class="col-lg-6">
            <div class="card card-coffee border-0 shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold font-serif text-dark mb-0">Daftar Alamat Pengiriman</h5>
                    <button class="btn btn-outline-caramel btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#newAddressForm" aria-expanded="false">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Alamat
                    </button>
                </div>

                <!-- Add Address Collapse Form -->
                <div class="collapse mb-4" id="newAddressForm">
                    <div class="p-3 border rounded-3 bg-light">
                        <h6 class="fw-bold small text-dark mb-3">Form Alamat Baru</h6>
                        <form action="{{ route('profile.addresses.store') }}" method="POST">
                            @csrf
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Label (Contoh: Rumah, Kantor)</label>
                                    <input type="text" name="label" class="form-control form-control-sm" required placeholder="Rumah">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Nama Penerima</label>
                                    <input type="text" name="recipient_name" class="form-control form-control-sm" required value="{{ $user->name }}">
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold">No. Telepon / WhatsApp</label>
                                <input type="text" name="phone" class="form-control form-control-sm" required value="{{ $user->phone }}">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold">Alamat Lengkap</label>
                                <textarea name="address_line" rows="2" class="form-control form-control-sm" required placeholder="Nama jalan, nomor rumah, RT/RW..."></textarea>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Kota</label>
                                    <input type="text" name="city" class="form-control form-control-sm" required placeholder="Contoh: Jakarta Selatan">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Kode Pos</label>
                                    <input type="text" name="postal_code" class="form-control form-control-sm" placeholder="12190">
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_default" value="1" id="isDefault">
                                <label class="form-check-label small" for="isDefault">
                                    Jadikan alamat utama
                                </label>
                            </div>

                            <button type="submit" class="btn btn-caramel btn-sm px-3">Simpan Alamat</button>
                        </form>
                    </div>
                </div>

                <!-- Addresses List -->
                @forelse($addresses as $addr)
                    <div class="p-3 border rounded-3 mb-2 bg-white position-relative">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div>
                                <span class="badge bg-light text-dark border me-1">{{ $addr->label }}</span>
                                @if($addr->is_default)
                                    <span class="badge bg-success">Utama</span>
                                @endif
                                <strong class="text-dark ms-1">{{ $addr->recipient_name }}</strong>
                            </div>
                            <form action="{{ route('profile.addresses.delete', $addr->id) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus alamat ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm text-danger p-0 border-0" title="Hapus Alamat">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                        <p class="small text-muted mb-1">{{ $addr->address_line }}, {{ $addr->city }} {{ $addr->postal_code }}</p>
                        <small class="text-muted"><i class="bi bi-telephone me-1"></i> {{ $addr->phone }}</small>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-geo-alt fs-2 d-block mb-2 text-caramel opacity-50"></i>
                        Belum ada alamat pengiriman yang tersimpan. Klik "Tambah Alamat" di atas untuk mempermudah pemesanan berikutnya.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
