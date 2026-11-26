@extends('layouts.app')

@section('title', 'Tentang Kami - RASA KOPI')

@section('content')

<!-- Header Banner -->
<section class="py-5 text-center text-white" style="background: linear-gradient(135deg, #2c1810 0%, #3e2723 100%);">
    <div class="container py-4">
        <span class="badge badge-caramel mb-2 px-3 py-2 text-uppercase">Profil Perusahaan</span>
        <h1 class="display-5 fw-bold font-serif mb-2">Cerita di Balik RASA KOPI</h1>
        <p class="lead text-light opacity-90 mx-auto" style="max-width: 600px;">
            Menghubungkan kebaikan petani kopi nusantara dengan kehangatan cangkir Anda setiap hari.
        </p>
    </div>
</section>

<!-- Company History & Story -->
<section class="container py-5 my-3">
    <div class="row align-items-center g-5">
        <div class="col-lg-6">
            <span class="text-caramel fw-bold text-uppercase small">Sejarah Singkat</span>
            <h2 class="fw-bold font-serif text-dark mb-4">Lahir dari Kecintaan Terhadap Kopi Indonesia</h2>
            <p class="text-muted leading-relaxed">
                {{ $storeSettings['story'] }}
            </p>
            <p class="text-muted leading-relaxed">
                Bermula dari sebuah kedai kecil dengan mesin sangrai sederhana, kami bertekad mempertemukan pecinta kopi dengan kopi nusantara murni tanpa bahan pengawet dan perisa buatan. Kini RASA KOPI terus berkembang melayani ribuan cangkir setiap minggu dengan komitmen kualitas yang sama.
            </p>
            <div class="row mt-4 pt-3 border-top g-3">
                <div class="col-4 text-center">
                    <h3 class="fw-bold text-caramel font-serif mb-0">100%</h3>
                    <small class="text-muted">Arabika &amp; Robusta Lokal</small>
                </div>
                <div class="col-4 text-center">
                    <h3 class="fw-bold text-caramel font-serif mb-0">15+</h3>
                    <small class="text-muted">Petani Mitra Langsung</small>
                </div>
                <div class="col-4 text-center">
                    <h3 class="fw-bold text-caramel font-serif mb-0">50K+</h3>
                    <small class="text-muted">Cangkir Terjual</small>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="position-relative">
                <img src="https://images.unsplash.com/photo-1442512595331-e89e73853f31?auto=format&fit=crop&w=800&q=80" alt="Roastery Rasa Kopi" class="img-fluid rounded-4 shadow-lg w-100" style="max-height: 440px; object-fit: cover;">
                <div class="position-absolute bottom-0 end-0 bg-caramel text-white p-3 rounded-3 shadow m-3 text-center">
                    <div class="fw-bold fs-5 font-serif">Fresh Roastery</div>
                    <small>Disangrai Langsung di Gerai</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vision & Mission -->
<section class="py-5" style="background-color: #f7efe5;">
    <div class="container py-3">
        <div class="row g-4">
            <!-- Visi -->
            <div class="col-md-6">
                <div class="card h-100 p-4 border-0 rounded-4 shadow-sm bg-white">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-cream p-3 text-caramel fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-eye"></i>
                        </div>
                        <h3 class="fw-bold font-serif text-dark mb-0">Visi Kami</h3>
                    </div>
                    <p class="text-muted mb-0 leading-relaxed">
                        {{ $storeSettings['vision'] }}
                    </p>
                </div>
            </div>

            <!-- Misi -->
            <div class="col-md-6">
                <div class="card h-100 p-4 border-0 rounded-4 shadow-sm bg-white">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-cream p-3 text-caramel fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-bullseye"></i>
                        </div>
                        <h3 class="fw-bold font-serif text-dark mb-0">Misi Kami</h3>
                    </div>
                    <div class="text-muted leading-relaxed">
                        {!! nl2br(e($storeSettings['mission'])) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Values -->
<section class="container py-5 my-3">
    <div class="text-center mb-5">
        <span class="text-caramel fw-bold text-uppercase small">Prinsip &amp; Fondasi</span>
        <h2 class="fw-bold font-serif text-dark">Nilai-Nilai Perusahaan</h2>
        <p class="text-muted small mx-auto" style="max-width: 500px;">Nilai utama yang kami pegang teguh dalam setiap cangkir yang kami sajikan.</p>
    </div>

    <div class="row g-4 text-center">
        <div class="col-md-3">
            <div class="p-4 rounded-4 bg-white shadow-sm h-100 border">
                <div class="text-caramel fs-1 mb-2"><i class="bi bi-check-circle"></i></div>
                <h5 class="fw-bold font-serif text-dark">Keaslian (Authenticity)</h5>
                <p class="text-muted small mb-0">Menjaga kemurnian biji kopi lokal tanpa campuran sintetis demi rasa asli tanah air.</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-4 rounded-4 bg-white shadow-sm h-100 border">
                <div class="text-caramel fs-1 mb-2"><i class="bi bi-heart"></i></div>
                <h5 class="fw-bold font-serif text-dark">Kehangatan (Hospitality)</h5>
                <p class="text-muted small mb-0">Menyambut setiap pelanggan bagai kawan lama dengan senyum dan pelayanan tulus.</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-4 rounded-4 bg-white shadow-sm h-100 border">
                <div class="text-caramel fs-1 mb-2"><i class="bi bi-tree"></i></div>
                <h5 class="fw-bold font-serif text-dark">Keberlanjutan (Fair Trade)</h5>
                <p class="text-muted small mb-0">Membeli biji kopi dengan harga adil dari petani lokal untuk kesejahteraan bersama.</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-4 rounded-4 bg-white shadow-sm h-100 border">
                <div class="text-caramel fs-1 mb-2"><i class="bi bi-shield-check"></i></div>
                <h5 class="fw-bold font-serif text-dark">Higienis &amp; Aman</h5>
                <p class="text-muted small mb-0">Standar sanitasi ketat pada setiap peralatan seduh, kemasan, dan proses penyajian.</p>
            </div>
        </div>
    </div>
</section>

@endsection
