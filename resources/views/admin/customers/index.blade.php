@extends('layouts.admin')

@section('page_title', 'Manajemen Pelanggan')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted small mb-0">Daftar seluruh pelanggan terdaftar beserta riwayat total transaksi belanja.</p>
</div>

<!-- Search Bar -->
<div class="card border-0 shadow-sm rounded-3 p-3 bg-white mb-4">
    <form action="{{ route('admin.customers.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-10">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama pelanggan, email, atau nomor HP...">
            </div>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-caramel btn-sm w-100"><i class="bi bi-funnel"></i> Cari</button>
            @if(request('q'))
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>
</div>

<div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th scope="col" class="ps-4">Pelanggan</th>
                    <th scope="col">Kontak</th>
                    <th scope="col">Tanggal Bergabung</th>
                    <th scope="col" class="text-center">Total Pesanan</th>
                    <th scope="col" class="text-end">Total Belanja</th>
                    <th scope="col" class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $cust)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-caramel text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($cust->name, 0, 1)) }}
                                </div>
                                <div>
                                    <strong class="text-dark d-block">{{ $cust->name }}</strong>
                                    <small class="text-muted">{{ $cust->email }}</small>
                                </div>
                            </div>
                        </td>

                        <td class="small">
                            @if($cust->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $cust->phone) }}" target="_blank" class="text-success text-decoration-none">
                                    <i class="bi bi-whatsapp me-1"></i> {{ $cust->phone }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td class="small text-muted">
                            {{ $cust->created_at->format('d M Y') }}
                        </td>

                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $cust->orders_count }} Pesanan</span>
                        </td>

                        <td class="text-end fw-bold text-dark">
                            Rp {{ number_format((float) ($cust->total_spent ?? 0), 0, ',', '.') }}
                        </td>

                        <td class="text-end pe-4">
                            <a href="{{ route('admin.customers.show', $cust->id) }}" class="btn btn-outline-primary btn-sm p-1 px-2" title="Detail Pelanggan">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada pelanggan terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 border-top d-flex justify-content-center">
        {{ $customers->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
