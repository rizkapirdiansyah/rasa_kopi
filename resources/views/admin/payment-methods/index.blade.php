@extends('layouts.admin')

@section('page_title', 'Manajemen Metode Pembayaran')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted small mb-0">Atur nomor rekening bank, nama pemilik rekening, petunjuk transfer, dan status aktif metode pembayaran.</p>
</div>

<div class="row g-4">
    @foreach($methods as $method)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle p-2 bg-light text-caramel fs-4">
                            <i class="bi {{ $method->icon ?? 'bi-wallet2' }}"></i>
                        </span>
                        <div>
                            <h6 class="fw-bold text-dark mb-0 font-serif">{{ $method->name }}</h6>
                            <small class="text-muted font-monospace">Code: {{ $method->code }}</small>
                        </div>
                    </div>
                    <div>
                        @if($method->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3 small flex-grow-1">
                    @if($method->account_number)
                        <div class="mb-1">
                            <span class="text-muted">Nomor Rekening:</span>
                            <span class="font-monospace fw-bold text-dark">{{ $method->account_number }}</span>
                        </div>
                    @endif
                    @if($method->account_name)
                        <div class="mb-1">
                            <span class="text-muted">Atas Nama:</span>
                            <span class="fw-semibold text-dark">{{ $method->account_name }}</span>
                        </div>
                    @endif
                    <div class="mt-2 text-muted">
                        {{ $method->instructions ?? 'Tidak ada petunjuk khusus.' }}
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <form action="{{ route('admin.payment-methods.toggle', $method->id) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn {{ $method->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} btn-sm">
                            <i class="bi {{ $method->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                            <span>{{ $method->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                        </button>
                    </form>
                    <a href="{{ route('admin.payment-methods.edit', $method->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil-square"></i> Edit Rekening
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

@endsection
