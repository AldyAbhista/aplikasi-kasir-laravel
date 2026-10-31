<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{font-family:'Inter',sans-serif;}
        :root{--primary:#059669;--grad:linear-gradient(135deg,#059669 0%,#0D9488 100%);--bg:#f0fdf4;--shadow:0 4px 20px rgba(5,150,105,0.10);}
        body{background:var(--bg);color:#1e293b;}
        .app-nav{background:var(--grad);padding:0.9rem 1.75rem;box-shadow:0 4px 24px rgba(5,150,105,0.28);}
        .brand{color:white;font-weight:800;font-size:1.15rem;display:flex;align-items:center;gap:0.6rem;}
        .btn-nav-back{background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:white;border-radius:10px;padding:0.4rem 1rem;font-weight:600;font-size:0.88rem;text-decoration:none;transition:all 0.2s;display:inline-flex;align-items:center;gap:0.4rem;}
        .btn-nav-back:hover{background:rgba(255,255,255,0.25);color:white;}
        .btn-nav-out{background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:white;border-radius:10px;padding:0.4rem 1rem;font-weight:600;font-size:0.88rem;transition:all 0.2s;}
        .btn-nav-out:hover{background:rgba(255,100,100,0.25);border-color:rgba(255,150,150,0.5);color:white;}
        .stat-card{background:white;border-radius:16px;padding:1.25rem 1.4rem;box-shadow:var(--shadow);border:none;transition:all 0.25s;}
        .stat-card:hover{transform:translateY(-3px);}
        .stat-icon{width:40px;height:40px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin-bottom:0.7rem;}
        .page-card{background:white;border-radius:20px;box-shadow:var(--shadow);padding:2rem;}
        .table-premium thead tr th{background:var(--grad);color:white;font-weight:600;font-size:0.8rem;letter-spacing:0.4px;padding:0.9rem 1rem;border:none;}
        .table-premium thead tr th:first-child{border-radius:12px 0 0 0;}
        .table-premium thead tr th:last-child{border-radius:0 12px 0 0;}
        .table-premium tbody tr{border-bottom:1px solid #f0fdf4;transition:background 0.15s;}
        .table-premium tbody tr:hover{background:#f0fdf4;}
        .table-premium tbody td{padding:0.85rem 1rem;vertical-align:middle;border:none;}
        .badge-cash{background:#dcfce7;color:#166534;border-radius:8px;padding:3px 10px;font-size:0.78rem;font-weight:700;}
        .badge-qris{background:#dbeafe;color:#1e40af;border-radius:8px;padding:3px 10px;font-size:0.78rem;font-weight:700;}
        .badge-today{background:#fef3c7;color:#92400e;border-radius:8px;padding:2px 8px;font-size:0.72rem;font-weight:700;}
        .btn-struk{background:#f0fdf4;color:#059669;border:1.5px solid #a7f3d0;border-radius:9px;padding:5px 12px;font-weight:600;font-size:0.8rem;text-decoration:none;transition:all 0.2s;display:inline-flex;align-items:center;gap:4px;}
        .btn-struk:hover{background:var(--primary);color:white;border-color:var(--primary);}
        .btn-void{background:#fee2e2;color:#991b1b;border:none;border-radius:9px;padding:5px 10px;font-weight:600;font-size:0.8rem;transition:all 0.2s;}
        .btn-void:hover{background:#ef4444;color:white;}
        .search-input{border:1.5px solid #e5e7eb;border-radius:10px;padding:0.55rem 1rem 0.55rem 2.3rem;font-size:0.88rem;transition:all 0.2s;width:220px;}
        .search-input:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(5,150,105,0.12);outline:none;}
        .total-bar{background:var(--grad);border-radius:14px;padding:1rem 1.5rem;color:white;margin-top:1.25rem;display:flex;align-items:center;justify-content:space-between;}
    </style>
</head>
<body>
    <nav class="app-nav d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            @if(auth()->user()?->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="btn-nav-back"><i class="bi bi-arrow-left"></i> Dashboard</a>
            @else
                <a href="{{ route('kasir.index') }}" class="btn-nav-back"><i class="bi bi-arrow-left"></i> Kembali</a>
            @endif
            <div class="brand"><i class="bi bi-clock-history"></i> Riwayat Transaksi</div>
        </div>
        @if(auth()->user()?->role === 'kasir')
        <form action="{{ route('logout') }}" method="POST" class="d-inline">@csrf
            <button type="submit" class="btn-nav-out"><i class="bi bi-box-arrow-right me-1"></i>Keluar</button>
        </form>
        @endif
    </nav>

    <div class="container-fluid px-4 py-4">
        @php
            $todayTrxCount = $transactions->filter(fn($t)=>$t->created_at->isToday())->count();
            $todayRevenue  = $transactions->filter(fn($t)=>$t->created_at->isToday())->sum('total_harga');
        @endphp

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#f0fdf4;color:#059669;"><i class="bi bi-cash-coin"></i></div>
                    <div style="font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.4px;">Total Pemasukan</div>
                    <div style="font-size:1.4rem;font-weight:800;color:#059669;margin-top:0.2rem;">Rp {{ number_format($total_pendapatan,0,',','.') }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#f0fdfa;color:#0D9488;"><i class="bi bi-receipt-cutoff"></i></div>
                    <div style="font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.4px;">Total Transaksi</div>
                    <div style="font-size:1.6rem;font-weight:800;color:#0D9488;margin-top:0.2rem;">{{ $transactions->count() }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fef3c7;color:#F59E0B;"><i class="bi bi-calendar-day"></i></div>
                    <div style="font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.4px;">Transaksi Hari Ini</div>
                    <div style="font-size:1.6rem;font-weight:800;color:#d97706;margin-top:0.2rem;">{{ $todayTrxCount }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#ede9fe;color:#8B5CF6;"><i class="bi bi-graph-up"></i></div>
                    <div style="font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.4px;">Penjualan Hari Ini</div>
                    <div style="font-size:1.25rem;font-weight:800;color:#7c3aed;margin-top:0.2rem;">Rp {{ number_format($todayRevenue,0,',','.') }}</div>
                </div>
            </div>
        </div>

        <div class="page-card">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <h5 style="font-weight:800;margin:0;">Daftar Transaksi</h5>
                <div style="position:relative;">
                    <i class="bi bi-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:0.85rem;"></i>
                    <form method="GET" action="{{ route('kasir.history') }}" class="d-flex gap-2">
                        <input type="search" name="search" value="{{ request('search') }}" class="search-input" placeholder="Cari nota / pembeli / tanggal..." aria-label="Cari transaksi">
                        <button type="submit" class="btn btn-success">Cari</button>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-premium" style="border-collapse:separate;border-spacing:0;">
                    <thead><tr>
                        <th>No</th><th>Tanggal</th><th>ID Transaksi</th><th>Pembeli</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-center">Metode</th>
                        <th class="text-end">Bayar</th><th class="text-end">Kembali</th>
                        <th class="text-end">Total</th><th class="text-center">Aksi</th>
                    </tr></thead>
                    <tbody id="bodyTrx">
                        @forelse($transactions as $i => $t)
                        <tr data-search="{{ strtolower($t->no_nota.' '.($t->customer_name??'').' '.$t->created_at->format('d/m/Y H:i')) }}">
                            <td style="color:#94a3b8;font-weight:600;">{{ $i+1 }}</td>
                            <td>
                                <div style="font-weight:600;font-size:0.88rem;">{{ $t->created_at->format('d/m/Y') }}</div>
                                <div style="color:#94a3b8;font-size:0.78rem;">{{ $t->created_at->format('H:i') }}</div>
                            </td>
                            <td>
                                <span style="background:#f0fdf4;color:#065f46;border-radius:8px;padding:3px 10px;font-weight:700;font-size:0.8rem;">{{ $t->no_nota }}</span>
                                @if($t->created_at->isToday()) <span class="badge-today ms-1">Hari ini</span> @endif
                            </td>
                            <td style="font-weight:600;font-size:0.88rem;">
                                {{ $t->customer_name ?? '-' }}
                            </td>
                            <td class="text-center">
                                @if(($t->order_type ?? 'dine_in') === 'takeaway')
                                    <span class="badge" style="background:#ffedd5;color:#c2410c;border-radius:8px;padding:3px 10px;font-size:0.78rem;font-weight:700;"><i class="bi bi-bag-check me-1"></i>Takeaway</span>
                                @else
                                    <span class="badge" style="background:#e0f2fe;color:#0369a1;border-radius:8px;padding:3px 10px;font-size:0.78rem;font-weight:700;"><i class="bi bi-cup-hot me-1"></i>Dine In</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($t->payment_method==='cash')
                                    <span class="badge-cash"><i class="bi bi-cash-coin me-1"></i>Cash</span>
                                @else
                                    <span class="badge-qris"><i class="bi bi-phone me-1"></i>QRIS {{ $t->bank??'' }}</span>
                                @endif
                            </td>
                            <td class="text-end" style="font-size:0.9rem;">Rp {{ number_format($t->bayar,0,',','.') }}</td>
                            <td class="text-end" style="font-size:0.9rem;color:#64748b;">Rp {{ number_format($t->kembalian,0,',','.') }}</td>
                            <td class="text-end" style="font-weight:800;color:#059669;">Rp {{ number_format($t->total_harga,0,',','.') }}</td>
                            <td class="text-center">
                                <a href="{{ route('kasir.struk', $t->id) }}" target="_blank" class="btn-struk"><i class="bi bi-printer"></i> Struk</a>
                                @if(auth()->user()->role === 'admin')
                                <form action="{{ route('laporan.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan transaksi ini?\nStok akan dikembalikan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-void ms-1"><i class="bi bi-x-circle"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center py-5" style="color:#94a3b8;"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Belum ada riwayat transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div id="noTrxMsg" class="text-center py-4" style="color:#94a3b8;display:none;"><i class="bi bi-search fs-2 d-block mb-2"></i>Transaksi tidak ditemukan.</div>
            </div>

            @if($transactions->hasPages())
            <div class="mt-3">
                {{ $transactions->links() }}
            </div>
            @endif

            <div class="total-bar">
                <span style="font-weight:700;font-size:0.95rem;"><i class="bi bi-calculator me-2"></i>Total Keseluruhan Pemasukan</span>
                <span style="font-weight:800;font-size:1.25rem;">Rp {{ number_format($total_pendapatan,0,',','.') }}</span>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.navigation && window.performance.navigation.type === 2)) {
                window.location.replace("{{ route('login') }}");
            }
        });
    </script>
</body>
</html>