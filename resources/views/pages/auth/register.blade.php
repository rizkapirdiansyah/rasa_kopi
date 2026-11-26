@extends('layouts.app')

@section('title', 'Daftar Akun Baru - RASA KOPI')

@section('content')

<div class="container py-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card card-coffee border-0 shadow-sm p-4 p-md-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold font-serif text-dark mb-1">Daftar Akun Baru</h2>
                    <p class="text-muted small">Bergabunglah dan nikmati berbagai promo kopi eksklusif dari Rasa Kopi.</p>
                </div>

                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="Contoh: Budi Santoso">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="email@domain.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor WhatsApp / HP</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-whatsapp"></i></span>
                            <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" required placeholder="Contoh: 081234567890">
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kata Sandi</label>
                            <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ulangi Kata Sandi</label>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="Konfirmasi sandi">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-caramel btn-lg w-100 py-2 mb-3 shadow-sm">
                        Buat Akun Sekarang
                    </button>

                    <div class="text-center small text-muted">
                        Sudah punya akun? <a href="{{ route('login') }}" class="text-caramel fw-bold text-decoration-none">Masuk di sini</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
