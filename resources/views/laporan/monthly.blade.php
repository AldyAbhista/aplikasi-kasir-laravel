<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{font-family:'Inter',sans-serif;}
        :root{--primary:#059669;--grad:linear-gradient(135deg,#059669 0%,#0D9488 100%);--bg:#f0fdf4;--shadow:0 4px 20px rgba(5,150,105,0.10);}
        body{background:var(--bg);color:#1e293b;}
        .app-nav{background:var(--grad);padding:0.9rem 1.75rem;box-shadow:0 4px 24px rgba(5,150,105,0.28);}
        .btn-nav-back{background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:white;border-radius:10px;padding:0.4rem 1rem;font-weight:600;font-size:0.88rem;text-decoration:none;transition:all 0.2s;display:inline-flex;align-items:center;gap:0.4rem;}
        .btn-nav-back:hover{background:rgba(255,255,255,0.25);color:white;}
        .tab-pill{background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:rgba(255,255,255,0.85);border-radius:10px;padding:0.4rem 1rem;font-weight:600;font-size:0.88rem;text-decoration:none;transition:all 0.2s;}
        .tab-pill:hover,.tab-pill.active{background:white;color:var(--primary);}
        .page-card{background:white;border-radius:20px;box-shadow:var(--shadow);padding:1.75rem;margin-bottom:1.5rem;}
        .stat-card{background:white;border-radius:16px;padding:1.25rem 1.4rem;box-shadow:var(--shadow);border:none;}
        .table-premium thead tr th{background:var(--grad);color:white;font-weight:600;font-size:0.8rem;padding:0.85rem 1rem;border:none;}
        .table-premium thead tr th:first-child{border-radius:12px 0 0 0;}
        .table-premium thead tr th:last-child{border-radius:0 12px 0 0;}
        .table-premium tbody tr{border-bottom:1px solid #f0fdf4;transition:background 0.15s;}
        .table-premium tbody tr:hover{background:#f0fdf4;}
        .table-premium tbody td{padding:0.82rem 1rem;border:none;font-size:0.88rem;}
        .badge-cash{background:#dcfce7;color:#166534;border-radius:8px;padding:3px 10px;font-size:0.78rem;font-weight:700;}
        .badge-qris{background:#dbeafe;color:#1e40af;border-radius:8px;padding:3px 10px;font-size:0.78rem;font-weight:700;}
        .btn-filter{background:var(--grad);border:none;color:white;border-radius:10px;padding:0.62rem 1.25rem;font-weight:700;font-size:0.88rem;transition:all 0.2s;}
        .btn-filter:hover{opacity:0.9;color:white;}
        .summary-card { border-left: 4px solid var(--primary); }
        @media print{.app-nav,.no-print{display:none!important;}body{background:white;}}
    </style>
</head>
<body>
    <nav class="app-nav d-flex align-items-center justify-content-between flex-wrap gap-2 no-print">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn-nav-back"><i class="bi bi-arrow-left"></i> Dashboard</a>
            <div style="color:white;font-weight:800;font-size:1.1rem;display:flex;align-items:center;gap:0.6rem;"><i class="bi bi-calendar-range"></i> Laporan Detail Penjualan</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('laporan.index') }}" class="tab-pill">📊 Dashboard</a>
            <a href="{{ route('laporan.monthly') }}" class="tab-pill active">📋 Detail Penjualan</a>
        </div>
    </nav>

    <!-- Alert & Filter -->
    <div class="container-fluid px-4 mb-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-funnel"></i> Filter Laporan</h6>
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Dari Tanggal</label>
                        <input type="date" id="filterStartDate" class="form-control" value="{{ $start_date }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Sampai Tanggal</label>
                        <input type="date" id="filterEndDate" class="form-control" value="{{ $end_date }}">
                    </div>
                    <div class="col-md-2">
                        <button class="btn fw-bold w-100 text-white" style="background:linear-gradient(135deg,#059669,#0D9488);border:none;border-radius:10px;" onclick="filterByDateRange()">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary fw-bold w-100" onclick="resetFilter()">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>
                    <div class="col-md-2 text-end">
                        <div class="btn-group w-100">
                            <a href="{{ route('laporan.export', ['start_date' => $start_date, 'end_date' => $end_date]) }}" class="btn btn-success btn-sm fw-bold">
                                <i class="bi bi-download"></i> Export
                            </a>
                            <button class="btn btn-outline-dark btn-sm fw-bold" onclick="window.print()">
                                <i class="bi bi-printer"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Bulanan -->
    <div class="container-fluid px-4 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 rounded-3" style="border-left: 5px solid #198754;">
                    <div class="card-body">
                        <h6 class="text-muted mb-2"><i class="bi bi-cash-coin"></i> Total Penjualan</h6>
                        <h4 class="text-success fw-bold">Rp {{ number_format($monthly_revenue, 0, ',', '.') }}</h4>
                        <small class="text-muted">Pendapatan total</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 rounded-3" style="border-left: 5px solid #fd7e14;">
                    <div class="card-body">
                        <h6 class="text-muted mb-2"><i class="bi bi-box-seam"></i> Total Modal</h6>
                        <h4 class="text-warning fw-bold" style="color: #856404;">Rp {{ number_format($monthly_modal, 0, ',', '.') }}</h4>
                        <small class="text-muted">Harga beli barang</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 rounded-3" style="border-left: 5px solid #0d6efd;">
                    <div class="card-body">
                        <h6 class="text-muted mb-2"><i class="bi bi-star-fill"></i> Keuntungan Bersih</h6>
                        <h4 class="fw-bold" style="color:#0D9488;">Rp {{ number_format($monthly_profit, 0, ',', '.') }}</h4>
                        <small class="text-muted">Profit penjualan</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 rounded-3" style="border-left: 5px solid #6f42c1;">
                    <div class="card-body">
                        <h6 class="text-muted mb-2"><i class="bi bi-receipt"></i> Jumlah Transaksi</h6>
                        <h4 class="text-primary fw-bold" style="color: #6f42c1;">{{ $monthly_transactions }}</h4>
                        <small class="text-muted">Transaksi tercatat</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Breakdown Metode Pembayaran -->
    @if($payment_breakdown->count() > 0)
    <div class="container-fluid px-4 mb-4">
        <div class="row">
            @foreach($payment_breakdown as $pb)
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">
                            @if($pb->payment_method === 'cash')
                                <i class="bi bi-cash-coin"></i> Pembayaran Cash
                            @else
                                <i class="bi bi-phone"></i> Pembayaran QRIS
                            @endif
                        </h6>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">Total</small>
                                <h5 class="fw-bold">Rp {{ number_format($pb->revenue, 0, ',', '.') }}</h5>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Transaksi</small>
                                <h5 class="fw-bold">{{ $pb->count }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Tabel Transaksi -->
    <div class="container-fluid px-4">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Daftar Transaksi</h5>

                <div class="table-responsive">
                    <table class="table table-hover align-middle detail-row">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Kasir</th>
                                <th>Tipe</th>
                                <th>Qty Barang</th>
                                <th>Total Penjualan</th>
                                <th>Modal</th>
                                <th>Keuntungan</th>
                                <th>Pembayaran</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $index => $t)
                                @php
                                    $profit_detail = $t->details->sum(function($d) {
                                        return ($d->harga_satuan - $d->harga_beli) * $d->jumlah;
                                    });
                                    $modal_detail = $t->details->sum(function($d) {
                                        return $d->harga_beli * $d->jumlah;
                                    });
                                    $total_qty = $t->details->sum('jumlah');
                                @endphp
                                <tr>
                                    <td><strong>{{ $index + 1 }}</strong></td>
                                    <td>
                                        <small class="text-muted">{{ $t->created_at->format('d/m/Y') }}</small><br>
                                        <strong>{{ $t->created_at->format('H:i') }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $t->user->name ?? '-' }}</span>
                                    </td>
                                    <td>
                                        @if(($t->order_type ?? 'dine_in') === 'takeaway')
                                            <span class="badge bg-warning text-dark"><i class="bi bi-bag-check"></i> Takeaway</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="bi bi-cup-hot"></i> Dine In</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-success">
                                        <i class="bi bi-box-seam"></i> {{ $total_qty }} unit
                                    </td>
                                    <td class="fw-bold text-success">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                                    <td><small class="text-muted">Rp {{ number_format($modal_detail, 0, ',', '.') }}</small></td>
                                    <td class="fw-bold" style="color:#0D9488;">Rp {{ number_format($profit_detail, 0, ',', '.') }}</td>
                                    <td>
                                        @if($t->payment_method === 'cash')
                                            <span class="badge bg-success"><i class="bi bi-cash-coin"></i> Cash</span>
                                        @else
                                            <span class="badge bg-info"><i class="bi bi-phone"></i> QRIS</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('kasir.struk', $t->id) }}" target="_blank" class="btn btn-outline-primary" title="Cetak Struk">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                            <a href="{{ route('laporan.edit', $t->id) }}" class="btn btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('laporan.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan transaksi ini? Stok akan dikembalikan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus/Batalkan">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Detail Produk (Collapsed) -->
                                <tr class="table-light">
                                    <td colspan="10">
                                        <button class="btn btn-sm btn-link text-start" data-bs-toggle="collapse" data-bs-target="#detail-{{ $t->id }}" aria-expanded="false">
                                            <i class="bi bi-chevron-right"></i> <strong>Detail Barang ({{ $t->details->count() }} item)</strong>
                                        </button>
                                        <div class="collapse" id="detail-{{ $t->id }}">
                                            <div class="p-3 bg-light rounded mt-2">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <thead>
                                                        <tr class="text-muted small">
                                                            <th>Barang</th>
                                                            <th class="text-center">Qty</th>
                                                            <th class="text-end">Harga Satuan</th>
                                                            <th class="text-end">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($t->details as $d)
                                                        <tr>
                                                            <td>{{ $d->product->nama_barang }}</td>
                                                            <td class="text-center"><span class="badge bg-warning text-dark">{{ $d->jumlah }}</span></td>
                                                            <td class="text-end">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                                            <td class="text-end fw-bold">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3"></i><br>Belum ada transaksi untuk periode ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        
                        <tfoot class="table-primary fw-bold">
                            <tr>
                                <td colspan="3" class="text-end">TOTAL PERIODE:</td>
                                <td></td>
                                <td><i class="bi bi-box-seam"></i> {{ $transactions->flatMap->details->sum('jumlah') }} unit</td>
                                <td>Rp {{ number_format($monthly_revenue, 0, ',', '.') }}</td>
                                <td><small>Rp {{ number_format($monthly_modal, 0, ',', '.') }}</small></td>
                                <td class="text-success">Rp {{ number_format($monthly_profit, 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterMonth() {
            const value = document.getElementById('filterMonth').value;
            const [year, month] = value.split('-');
            window.location.href = `{{ route('laporan.monthly') }}?month=${month}&year=${year}`;
        }

        function filterByDateRange() {
            const startDate = document.getElementById('filterStartDate').value;
            const endDate = document.getElementById('filterEndDate').value;
            
            if (!startDate || !endDate) {
                alert('Silakan pilih tanggal awal dan akhir');
                return;
            }

            if (startDate > endDate) {
                alert('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
                return;
            }

            window.location.href = `{{ route('laporan.monthly') }}?start_date=${startDate}&end_date=${endDate}`;
        }

        function resetFilter() {
            window.location.href = `{{ route('laporan.monthly') }}`;
        }
    </script>

    <!-- Bootstrap Bundle JS untuk Collapse/Accordion -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
