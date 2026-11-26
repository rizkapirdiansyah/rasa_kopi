<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }} - {{ $storeSettings['name'] }}</title>

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }
        .invoice-card {
            background-color: #ffffff;
            border-radius: 8px;
            max-width: 820px;
            margin: 30px auto;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        @media print {
            body {
                background-color: #ffffff;
            }
            .no-print {
                display: none !important;
            }
            .invoice-card {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Action buttons (hidden on print) -->
    <div class="d-flex justify-content-between align-items-center mt-4 mb-2 no-print" style="max-width: 820px; margin-left: auto; margin-right: auto;">
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm px-4">
            <i class="bi bi-printer me-1"></i> Cetak / Simpan PDF
        </button>
    </div>

    <!-- Printable Invoice -->
    <div class="invoice-card">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1" style="letter-spacing: 1px;">RASA<span style="color: #c67c4e;">KOPI</span></h2>
                <div class="text-muted small">{{ $storeSettings['address'] }}</div>
                <div class="text-muted small">Telepon/WA: {{ $storeSettings['phone'] }} | Email: {{ $storeSettings['email'] }}</div>
            </div>
            <div class="text-end">
                <h4 class="fw-bold text-uppercase text-secondary mb-1">INVOICE</h4>
                <div class="fw-bold font-monospace text-dark fs-5">{{ $order->order_number }}</div>
                <div class="small text-muted">Tanggal: {{ $order->created_at->format('d/m/Y H:i') }}</div>
                <div class="mt-2">
                    <span class="badge bg-secondary px-2 py-1">{{ $order->status_label }}</span>
                </div>
            </div>
        </div>

        <!-- Bill To & Ship To -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6">
                <h6 class="text-uppercase text-muted small fw-bold mb-2">Tagihan Kepada:</h6>
                <div class="fw-bold text-dark">{{ $order->customer_name }}</div>
                <div class="small text-muted">{{ $order->customer_phone }}</div>
                <div class="small text-muted">{{ $order->customer_email }}</div>
            </div>
            <div class="col-sm-6 text-sm-end">
                <h6 class="text-uppercase text-muted small fw-bold mb-2">Pengiriman &amp; Pembayaran:</h6>
                <div class="small text-dark">
                    <strong>Tipe:</strong> {{ $order->shipping_type === 'pickup' ? 'Ambil di Toko' : 'Pengiriman Kurir' }}
                </div>
                @if($order->shipping_type === 'delivery')
                    <div class="small text-muted">{{ $order->shipping_address }}, {{ $order->shipping_city }}</div>
                @else
                    <div class="small text-muted">Waktu Pick-up: {{ $order->pickup_time ?? 'Segera' }}</div>
                @endif
                <div class="small text-dark mt-1">
                    <strong>Metode Bayar:</strong> {{ $order->payment_method_name }}
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="table table-bordered mb-4">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th scope="col" style="width: 50px;" class="text-center">No</th>
                    <th scope="col">Nama Menu Kopi</th>
                    <th scope="col" class="text-end" style="width: 140px;">Harga Satuan</th>
                    <th scope="col" class="text-center" style="width: 80px;">Jumlah</th>
                    <th scope="col" class="text-end" style="width: 140px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $idx => $item)
                    <tr>
                        <td class="text-center small">{{ $idx + 1 }}</td>
                        <td>
                            <strong class="text-dark">{{ $item->product_name }}</strong>
                        </td>
                        <td class="text-end small">{{ $item->formatted_price }}</td>
                        <td class="text-center small">{{ $item->quantity }}</td>
                        <td class="text-end fw-semibold small">{{ $item->formatted_subtotal }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="4" class="text-end fw-semibold">Subtotal Produk:</td>
                    <td class="text-end fw-semibold">{{ $order->formatted_subtotal }}</td>
                </tr>
                @if($order->discount_amount > 0)
                    <tr>
                        <td colspan="4" class="text-end text-success">Diskon Kupon ({{ $order->coupon_code }}):</td>
                        <td class="text-end text-success">- {{ $order->formatted_discount }}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="4" class="text-end">Ongkos Kirim:</td>
                    <td class="text-end">{{ $order->formatted_shipping_cost }}</td>
                </tr>
                <tr class="table-active">
                    <td colspan="4" class="text-end fw-bold fs-6">Total Pembayaran:</td>
                    <td class="text-end fw-bold fs-6" style="color: #c67c4e;">{{ $order->formatted_total }}</td>
                </tr>
            </tfoot>
        </table>

        @if($order->customer_notes)
            <div class="p-3 bg-light rounded border mb-4 small">
                <strong>Catatan Khusus:</strong> {{ $order->customer_notes }}
            </div>
        @endif

        <!-- Footer / Signature -->
        <div class="row pt-4 border-top mt-4 text-center text-sm-start align-items-center">
            <div class="col-sm-8 small text-muted mb-3 mb-sm-0">
                <div>Terima kasih telah memesan kopi di <strong>{{ $storeSettings['name'] }}</strong>.</div>
                <div>Simpan invoice ini sebagai bukti transaksi resmi Anda.</div>
            </div>
            <div class="col-sm-4 text-sm-end">
                <div class="small text-muted mb-4">Kasir / Barista:</div>
                <div class="fw-bold text-dark border-bottom d-inline-block pb-1" style="min-width: 140px;">Tim Rasa Kopi</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
