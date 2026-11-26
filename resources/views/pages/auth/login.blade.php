@extends('layouts.app')

@section('title', 'Masuk Akun - RASA KOPI')

@section('content')

<div class="container py-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-coffee border-0 shadow-sm p-4 p-md-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold font-serif text-dark mb-1">Selamat Datang</h2>
                    <p class="text-muted small">Masuk ke akun Rasa Kopi Anda untuk melanjutkan pesanan.</p>
                </div>

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold mb-0">Kata Sandi</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control" required placeholder="Masukkan kata sandi">
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                        <label class="form-check-label small" for="rememberMe">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <button type="submit" class="btn btn-caramel btn-lg w-100 py-2 mb-3 shadow-sm">
                        Masuk ke Akun
                    </button>

                    <div class="text-center small text-muted">
                        Belum punya akun? <a href="{{ route('register') }}" class="text-caramel fw-bold text-decoration-none">Daftar sekarang</a>
                    </div>
                </form>

                <!-- Demo Credentials Helper -->
                <div class="mt-4 pt-3 border-top small text-muted">
                    <strong class="d-block mb-1 text-dark">Akun Demo:</strong>
                    <div><span class="badge bg-warning text-dark me-1">Admin:</span> admin@rasakopi.com / admin123</div>
                    <div class="mt-1"><span class="badge bg-secondary me-1">Pelanggan:</span> budi@gmail.com / password</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
