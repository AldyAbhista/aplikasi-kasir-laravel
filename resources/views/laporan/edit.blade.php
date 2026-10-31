<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi - Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{font-family:'Inter',sans-serif;}
        :root{--primary:#059669;--grad:linear-gradient(135deg,#059669 0%,#0D9488 100%);}
        body{background:#f0fdf4;color:#1e293b;}
        .app-nav{background:var(--grad);padding:0.9rem 1.75rem;box-shadow:0 4px 24px rgba(5,150,105,0.28);}
        .btn-nav-back{background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:white;border-radius:10px;padding:0.4rem 1rem;font-weight:600;font-size:0.88rem;text-decoration:none;display:inline-flex;align-items:center;gap:0.4rem;}
        .btn-nav-back:hover{background:rgba(255,255,255,0.25);color:white;}
        .page-card{background:white;border-radius:20px;box-shadow:0 4px 20px rgba(5,150,105,0.10);padding:2rem;}
        .form-control:focus,.form-select:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(5,150,105,0.12);}
        .info-box{background:#f0fdf4;border:1.5px solid #a7f3d0;border-radius:14px;padding:1.25rem;margin-bottom:1.5rem;}
        .btn-save{background:var(--grad);border:none;color:white;border-radius:12px;padding:0.75rem 2rem;font-weight:700;transition:all 0.3s;}
        .btn-save:hover{opacity:0.9;transform:translateY(-1px);color:white;}
    </style>

</head>
<body>
    <nav class="app-nav d-flex align-items-center gap-3">
        <a href="{{ route('laporan.monthly') }}" class="btn-nav-back"><i class="bi bi-arrow-left"></i> Laporan</a>
        <div style="color:white;font-weight:800;font-size:1.1rem;display:flex;align-items:center;gap:0.6rem;"><i class="bi bi-pencil-square"></i> Edit Transaksi</div>
    </nav>

    <div class="container py-4" style="max-width:780px;">
        <div class="page-card">
                        <h5 class="fw-bold mb-4"><i class="bi bi-receipt me-1" style="color:var(--primary);"></i> Detail Transaksi {{ $transaction->no_nota }}</h5>
                        <div class="info-box">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>ID Struk:</strong> {{ $transaction->no_nota }}<br>
                                    <strong>Tanggal:</strong> {{ $transaction->created_at->format('d/m/Y H:i') }}<br>
                                    <strong>Kasir:</strong> {{ $transaction->user->name }}
                                </div>
                                <div class="col-md-6 text-end">
                                    <strong>Total Item:</strong> {{ $transaction->details->count() }}<br>
                                    <strong>Status:</strong> <span class="badge bg-success">Selesai</span><br>
                                    <strong>Metode Pembayaran:</strong> 
                                    @if($transaction->payment_method === 'cash')
                                        <span class="badge bg-success"><i class="bi bi-cash-coin"></i> Cash</span>
                                    @else
                                        <span class="badge bg-info"><i class="bi bi-phone"></i> QRIS {{ $transaction->bank ?? '' }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Detail Items -->
                        <h6 class="fw-bold mt-4 mb-3">Detail Barang:</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Harga</th>
                                        <th class="text-end">Profit</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_modal = 0;
                                        $total_profit = 0;
                                    @endphp
                                    @foreach($transaction->details as $d)
                                        @php
                                            $modal = $d->harga_beli * $d->jumlah;
                                            $profit = ($d->harga_satuan - $d->harga_beli) * $d->jumlah;
                                            $total_modal += $modal;
                                            $total_profit += $profit;
                                        @endphp
                                        <tr>
                                            <td>{{ $d->product->nama_barang }}</td>
                                            <td class="text-end">{{ $d->jumlah }}</td>
                                            <td class="text-end">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                            <td class="text-end text-success fw-bold">Rp {{ number_format($profit, 0, ',', '.') }}</td>
                                            <td class="text-end">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-light fw-bold">
                                        <td colspan="2">TOTAL:</td>
                                        <td class="text-end">Modal: Rp {{ number_format($total_modal, 0, ',', '.') }}</td>
                                        <td class="text-end text-success">Profit: Rp {{ number_format($total_profit, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Edit Form -->
                        <h6 class="fw-bold mt-4 mb-3">Edit Pembayaran:</h6>
                        <form action="{{ route('laporan.update', $transaction->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-bold">Total Tagihan (tidak bisa diubah)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control form-control-lg" value="{{ number_format($transaction->total_harga, 0, ',', '.') }}" disabled>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Uang Bayar (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text fw-bold" style="background:#f0fdf4;border-color:#a7f3d0;color:#059669;">Rp</span>
                                    <input type="text" id="bayar-display" class="form-control form-control-lg fw-bold"
                                        value="{{ number_format($transaction->bayar, 0, ',', '.') }}"
                                        inputmode="numeric" autocomplete="off"
                                        style="border-color:#a7f3d0;letter-spacing:0.5px;"
                                        oninput="formatBayarAdmin(this)">
                                </div>
                                <input type="hidden" name="bayar" id="bayar-raw" value="{{ $transaction->bayar }}">
                                <small class="text-muted" style="font-size:0.78rem;"><i class="bi bi-info-circle"></i> Ketik angka, otomatis diformat (contoh: 30.000) &nbsp;· Min: Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Kembalian (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="kembalian" class="form-control form-control-lg" 
                                           value="{{ number_format($transaction->kembalian, 0, ',', '.') }}" disabled>
                                </div>
                            </div>

                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('laporan.monthly') }}" class="btn btn-secondary btn-lg" style="border-radius:12px;">Batal</a>
                                <button type="submit" class="btn-save btn-lg">
                                    <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>

        </div>
    </div>

    <script>
        const totalTagihan = {{ $transaction->total_harga }};

        // Format input uang bayar (titik ribuan otomatis)
        function formatBayarAdmin(el) {
            let raw = el.value.replace(/\D/g, '');
            let num = parseInt(raw) || 0;
            if (raw === '') {
                el.value = '';
                document.getElementById('bayar-raw').value = 0;
            } else {
                el.value = new Intl.NumberFormat('id-ID').format(num);
                document.getElementById('bayar-raw').value = num;
            }
            hitungKembalian();
        }

        function hitungKembalian() {
            const bayar = parseInt(document.getElementById('bayar-raw').value) || 0;
            const kembalian = bayar - totalTagihan;
            
            const formatted = new Intl.NumberFormat('id-ID').format(Math.max(0, kembalian));
            document.getElementById('kembalian').value = kembalian >= 0 ? formatted : 'Uang kurang!';
        }

        // Initialize on load
        hitungKembalian();
    </script>

</body>
</html>
