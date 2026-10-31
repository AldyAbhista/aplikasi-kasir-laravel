<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <style>
        *{font-family:'Inter',sans-serif;}
        :root{--primary:#059669;--accent:#0D9488;--grad:linear-gradient(135deg,#059669 0%,#0D9488 100%);--bg:#f0fdf4;--shadow:0 4px 20px rgba(5,150,105,0.10);--shadow-h:0 12px 40px rgba(5,150,105,0.20);}
        body{background:var(--bg);color:#1e293b;}
        .app-nav{background:var(--grad);padding:0.9rem 1.75rem;box-shadow:0 4px 24px rgba(5,150,105,0.28);}
        .brand{color:white;font-weight:800;font-size:1.15rem;display:flex;align-items:center;gap:0.6rem;}
        .btn-nav-back{background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:white;border-radius:10px;padding:0.4rem 1rem;font-weight:600;font-size:0.88rem;text-decoration:none;transition:all 0.2s;display:inline-flex;align-items:center;gap:0.4rem;}
        .btn-nav-back:hover{background:rgba(255,255,255,0.25);color:white;}
        .tab-pill{background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:rgba(255,255,255,0.85);border-radius:10px;padding:0.4rem 1rem;font-weight:600;font-size:0.88rem;text-decoration:none;transition:all 0.2s;}
        .tab-pill:hover,.tab-pill.active{background:white;color:var(--primary);}
        .stat-card{background:white;border-radius:18px;padding:1.4rem 1.5rem;box-shadow:var(--shadow);border:none;transition:all 0.3s;}
        .stat-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-h);}
        .stat-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:1rem;}
        .page-card{background:white;border-radius:20px;box-shadow:var(--shadow);padding:1.75rem;}
        .top-item{background:#f8fafc;border-radius:12px;padding:0.9rem 1rem;margin-bottom:0.6rem;display:flex;align-items:center;justify-content:space-between;border-left:3px solid var(--primary);transition:all 0.2s;}
        .top-item:hover{background:#f0fdf4;}
        .rank-badge{width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:0.8rem;}
        .method-card{border-radius:16px;padding:1.25rem;color:white;}
        .table-sm-lap thead th{background:var(--grad);color:white;font-size:0.8rem;font-weight:600;padding:0.75rem 1rem;border:none;}
        .table-sm-lap tbody tr{border-bottom:1px solid #f0fdf4;}
        .table-sm-lap tbody tr:hover{background:#f0fdf4;}
        .table-sm-lap tbody td{padding:0.75rem 1rem;border:none;font-size:0.88rem;}
    </style>
</head>
<body>
    <nav class="app-nav d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn-nav-back"><i class="bi bi-arrow-left"></i> Dashboard</a>
            <div class="brand"><i class="bi bi-graph-up-arrow"></i> Laporan Keuangan</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('laporan.index') }}" class="tab-pill active">📊 Dashboard</a>
            <a href="{{ route('laporan.monthly') }}" class="tab-pill">📋 Detail Penjualan</a>
        </div>
    </nav>

    <div class="container-fluid px-4 py-4">
        @php
            $margin = $total_pendapatan > 0 ? round(($total_keuntungan/$total_pendapatan)*100,2) : 0;
            $avg    = $total_transaksi > 0 ? $total_pendapatan/$total_transaksi : 0;
        @endphp

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#f0fdf4;color:#059669;"><i class="bi bi-cash-coin"></i></div>
                    <div style="font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.4px;">Total Pendapatan</div>
                    <div style="font-size:1.5rem;font-weight:800;color:#059669;margin:0.25rem 0;">Rp {{ number_format($total_pendapatan,0,',','.') }}</div>
                    <div style="font-size:0.76rem;color:#94a3b8;">sepanjang waktu</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fffbeb;color:#F59E0B;"><i class="bi bi-box-seam-fill"></i></div>
                    <div style="font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.4px;">Total Modal</div>
                    <div style="font-size:1.5rem;font-weight:800;color:#d97706;margin:0.25rem 0;">Rp {{ number_format($total_modal,0,',','.') }}</div>
                    <div style="font-size:0.76rem;color:#94a3b8;">harga beli barang</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#f0fdfa;color:#0D9488;"><i class="bi bi-star-fill"></i></div>
                    <div style="font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.4px;">Keuntungan Bersih</div>
                    <div style="font-size:1.5rem;font-weight:800;color:#0D9488;margin:0.25rem 0;">Rp {{ number_format($total_keuntungan,0,',','.') }}</div>
                    <div style="font-size:0.76rem;color:#94a3b8;">margin {{ $margin }}%</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#ede9fe;color:#8B5CF6;"><i class="bi bi-receipt-cutoff"></i></div>
                    <div style="font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.4px;">Total Transaksi</div>
                    <div style="font-size:1.65rem;font-weight:800;color:#7c3aed;margin:0.25rem 0;">{{ $total_transaksi }}</div>
                    <div style="font-size:0.76rem;color:#94a3b8;">avg Rp {{ number_format($avg,0,',','.') }}/trx</div>
                </div>
            </div>
        </div>

        <!-- Hari Ini + Metode -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="page-card h-100">
                    <div style="font-weight:700;font-size:0.95rem;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                        <i class="bi bi-calendar-day" style="color:var(--primary);"></i> Hari Ini
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div style="font-size:0.75rem;color:#94a3b8;">Pendapatan</div>
                            <div style="font-size:1.3rem;font-weight:800;color:#059669;">Rp {{ number_format($today_revenue,0,',','.') }}</div>
                        </div>
                        <div class="col-6">
                            <div style="font-size:0.75rem;color:#94a3b8;">Transaksi</div>
                            <div style="font-size:1.6rem;font-weight:800;color:#0D9488;">{{ $today_transactions }}</div>
                        </div>
                    </div>
                    <hr style="border-color:#f0fdf4;margin:1rem 0;">
                    <div class="row">
                        <div class="col-6">
                            <div style="font-size:0.75rem;color:#94a3b8;">Cash</div>
                            <div style="font-size:1.05rem;font-weight:700;color:#059669;">Rp {{ number_format($cash_revenue,0,',','.') }}</div>
                            <div style="font-size:0.75rem;color:#94a3b8;">{{ $cash_count }} trx</div>
                        </div>
                        <div class="col-6">
                            <div style="font-size:0.75rem;color:#94a3b8;">QRIS</div>
                            <div style="font-size:1.05rem;font-weight:700;color:#3b82f6;">Rp {{ number_format($qris_revenue,0,',','.') }}</div>
                            <div style="font-size:0.75rem;color:#94a3b8;">{{ $qris_count }} trx</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="col-md-4">
                <div class="page-card h-100">
                    <div style="font-weight:700;font-size:0.95rem;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                        <i class="bi bi-trophy-fill" style="color:#F59E0B;"></i> Top 5 Produk
                    </div>
                    @forelse($top_products as $i => $p)
                    <div class="top-item">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rank-badge" style="background:{{ $i===0?'#fbbf24':($i===1?'#94a3b8':($i===2?'#c4a475':'#f0fdf4')) }};color:{{ $i<3?'white':'#64748b' }};">{{ $i+1 }}</div>
                            <div>
                                <div style="font-weight:700;font-size:0.88rem;">{{ $p->product->nama_barang }}</div>
                                <div style="font-size:0.75rem;color:#94a3b8;">{{ $p->total_qty }} unit terjual</div>
                            </div>
                        </div>
                        <span style="background:#dcfce7;color:#166534;border-radius:8px;padding:3px 10px;font-weight:700;font-size:0.78rem;">Rp {{ number_format($p->total_sales,0,',','.') }}</span>
                    </div>
                    @empty
                    <div style="text-align:center;color:#94a3b8;padding:2rem 0;"><i class="bi bi-graph-down fs-2 d-block mb-2"></i>Belum ada penjualan</div>
                    @endforelse
                </div>
            </div>

            <!-- Bank QRIS -->
            <div class="col-md-4">
                <div class="page-card h-100">
                    <div style="font-weight:700;font-size:0.95rem;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                        <i class="bi bi-bank" style="color:#3b82f6;"></i> Detail Bank QRIS
                    </div>
                    @if($bank_breakdown->count() > 0)
                    <table class="table table-sm-lap" style="border-collapse:separate;border-spacing:0;border-radius:12px;overflow:hidden;">
                        <thead><tr><th>Bank/E-Wallet</th><th class="text-end">Trx</th><th class="text-end">Total</th></tr></thead>
                        <tbody>
                            @foreach($bank_breakdown as $b)
                            <tr>
                                <td style="font-weight:600;">{{ $b->bank??'Lainnya' }}</td>
                                <td class="text-end" style="color:#94a3b8;">{{ $b->count }}</td>
                                <td class="text-end" style="font-weight:700;color:#3b82f6;">Rp {{ number_format($b->revenue,0,',','.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div style="text-align:center;color:#94a3b8;padding:2rem 0;"><i class="bi bi-phone fs-2 d-block mb-2"></i>Belum ada transaksi QRIS</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Grafik -->
        <div class="page-card">
            <div style="font-weight:700;font-size:0.95rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:0.5rem;">
                <i class="bi bi-graph-up" style="color:var(--primary);"></i> Grafik Pendapatan 12 Bulan
            </div>
            <canvas id="chartRevenue" style="max-height:300px;"></canvas>
        </div>
    </div>

    <script>
        const monthlyData = @json($monthly_revenue);
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        const revenues = Array(12).fill(0);
        monthlyData.forEach(item => { revenues[item.month - 1] = item.revenue; });

        const ctx = document.getElementById('chartRevenue').getContext('2d');
        const grad = ctx.createLinearGradient(0,0,0,300);
        grad.addColorStop(0,'rgba(5,150,105,0.3)');
        grad.addColorStop(1,'rgba(5,150,105,0.01)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: revenues,
                    borderColor: '#059669',
                    backgroundColor: grad,
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: '#059669',
                    pointBorderColor: 'white',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f0fdf4' }, ticks: { color: '#94a3b8', font: { family: 'Inter' } } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { family: 'Inter' } } }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
