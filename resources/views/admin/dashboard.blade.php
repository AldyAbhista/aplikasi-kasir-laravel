<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{font-family:'Inter',sans-serif;}
        :root{--primary:#059669;--accent:#0D9488;--grad:linear-gradient(135deg,#059669 0%,#0D9488 100%);--bg:#f0fdf4;--shadow:0 4px 20px rgba(5,150,105,0.10);--shadow-h:0 12px 40px rgba(5,150,105,0.20);}
        body{background:var(--bg);color:#1e293b;}
        /* Navbar */
        .app-nav{background:var(--grad);padding:0.9rem 1.75rem;box-shadow:0 4px 24px rgba(5,150,105,0.28);}
        .app-nav .brand{color:white;font-weight:800;font-size:1.2rem;display:flex;align-items:center;gap:0.6rem;}
        .nav-user{color:rgba(255,255,255,0.9);font-size:0.9rem;}
        .btn-nav-out{background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:white;border-radius:10px;padding:0.4rem 1rem;font-weight:600;font-size:0.88rem;transition:all 0.2s;}
        .btn-nav-out:hover{background:rgba(255,100,100,0.25);border-color:rgba(255,150,150,0.5);color:white;}
        /* Stat Cards */
        .stat-card{background:white;border-radius:18px;padding:1.4rem 1.5rem;box-shadow:var(--shadow);border:none;transition:all 0.3s;position:relative;overflow:hidden;}
        .stat-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-h);}
        .stat-card::after{content:'';position:absolute;top:-20px;right:-20px;width:100px;height:100px;border-radius:50%;opacity:0.06;}
        .stat-card.green::after{background:#059669;}
        .stat-card.teal::after{background:#0D9488;}
        .stat-card.amber::after{background:#F59E0B;}
        .stat-card.red::after{background:#EF4444;}
        .stat-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:1rem;}
        .stat-label{font-size:0.8rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.3rem;}
        .stat-value{font-size:1.65rem;font-weight:800;color:#1e293b;line-height:1;}
        .stat-sub{font-size:0.78rem;color:#94a3b8;margin-top:0.35rem;}
        /* Alert Stock */
        .stock-alert{background:linear-gradient(135deg,#fef2f2,#fff5f5);border:1.5px solid #fecaca;border-radius:16px;padding:1.25rem 1.5rem;}
        /* Menu Cards */
        .menu-card{background:white;border-radius:18px;padding:1.75rem 1.5rem;box-shadow:var(--shadow);text-decoration:none;color:#1e293b;display:flex;flex-direction:column;align-items:center;justify-content:center;height:237px;transition:all 0.3s;border:2px solid transparent;}
        .menu-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-h);border-color:#a7f3d0;color:#1e293b;}
        .menu-card .m-icon{width:68px;height:68px;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 1rem;}
        .menu-card h5{font-weight:700;font-size:1rem;margin-bottom:0.4rem;}
        .menu-card p{font-size:0.83rem;color:#64748b;margin:0;}
        .blink{animation:blink 1.4s infinite;}
        @keyframes blink{0%,100%{opacity:1}50%{opacity:0.5}}
        .section-title{font-weight:700;font-size:1rem;color:#374151;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;}
        .section-title::after{content:'';flex:1;height:1px;background:#e5e7eb;margin-left:0.5rem;}
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="app-nav d-flex align-items-center justify-content-between">
        @php
            $storeName = \App\Models\StoreSetting::getSetting()->store_name ?? 'Kasir';
        @endphp
        <div class="brand">
            <div style="width:36px;height:36px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-shop" style="color:white;font-size:1.2rem;"></i>
            </div>
            {{ $storeName }} &nbsp;<span style="opacity:0.6;font-weight:400;font-size:0.95rem;">/ Admin</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="nav-user">Halo, <strong>{{ auth()->user()->name }}</strong></span>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn-nav-out"><i class="bi bi-box-arrow-right me-1"></i>Keluar</button>
            </form>
        </div>
    </nav>

    <div class="container-fluid px-4 py-4">
        @php
            $todayRevenue     = \App\Models\Transaction::whereDate('created_at', today())->sum('total_harga');
            $todayTrx         = \App\Models\Transaction::whereDate('created_at', today())->count();
            $totalRevenue     = \App\Models\Transaction::sum('total_harga');
            $totalTrx         = \App\Models\Transaction::count();
            $lowStockProducts = \App\Models\Product::where('stok', '<=', 5)->get();
            $totalProducts    = \App\Models\Product::count();
        @endphp

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-card green">
                    <div class="stat-icon" style="background:#f0fdf4;color:#059669;"><i class="bi bi-calendar-day-fill"></i></div>
                    <div class="stat-label">Pendapatan Hari Ini</div>
                    <div class="stat-value text-success">Rp {{ number_format($todayRevenue,0,',','.') }}</div>
                    <div class="stat-sub">{{ $todayTrx }} transaksi hari ini</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card teal">
                    <div class="stat-icon" style="background:#f0fdfa;color:#0D9488;"><i class="bi bi-receipt-cutoff"></i></div>
                    <div class="stat-label">Total Pendapatan</div>
                    <div class="stat-value" style="color:#0D9488;">Rp {{ number_format($totalRevenue,0,',','.') }}</div>
                    <div class="stat-sub">{{ $totalTrx }} transaksi semua waktu</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card amber">
                    <div class="stat-icon" style="background:#fffbeb;color:#F59E0B;"><i class="bi bi-box-seam-fill"></i></div>
                    <div class="stat-label">Total Produk</div>
                    <div class="stat-value" style="color:#d97706;">{{ $totalProducts }}</div>
                    <div class="stat-sub">produk terdaftar</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card {{ $lowStockProducts->count() > 0 ? 'red' : 'green' }}">
                    <div class="stat-icon" style="background:{{ $lowStockProducts->count() > 0 ? '#fef2f2' : '#f0fdf4' }};color:{{ $lowStockProducts->count() > 0 ? '#EF4444' : '#059669' }};"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div class="stat-label">Stok Menipis</div>
                    <div class="stat-value {{ $lowStockProducts->count() > 0 ? 'text-danger' : 'text-success' }}">{{ $lowStockProducts->count() }}</div>
                    <div class="stat-sub">produk stok ≤ 5</div>
                </div>
            </div>
        </div>

        <!-- Alert Stok -->
        @if($lowStockProducts->count() > 0)
        <div class="stock-alert mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <div style="font-weight:700;color:#991b1b;margin-bottom:0.5rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $lowStockProducts->count() }} produk memiliki stok menipis!</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($lowStockProducts as $lp)
                            <span class="badge blink" style="background:#fecaca;color:#991b1b;font-size:0.8rem;border-radius:8px;padding:5px 10px;">{{ $lp->nama_barang }} ({{ $lp->stok }})</span>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-sm fw-bold" style="background:#ef4444;color:white;border-radius:10px;white-space:nowrap;">
                    <i class="bi bi-arrow-right me-1"></i> Update Stok
                </a>
            </div>
        </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success border-0 rounded-4 py-3 mb-4 shadow-sm" style="background:#f0fdf4;color:#065f46;">
                <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 rounded-4 py-3 mb-4 shadow-sm">
                <i class="bi bi-exclamation-circle-fill me-2 fs-5 align-middle"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Menu Grid -->
        <div class="section-title"><i class="bi bi-grid-3x3-gap-fill" style="color:var(--primary);"></i> Menu Utama</div>
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <a href="{{ route('kasir.index') }}" target="_blank" class="menu-card text-center">
                    <div class="m-icon" style="background:linear-gradient(135deg,#059669,#0D9488);color:white;"><i class="bi bi-shop"></i></div>
                    <h5>Buka Kasir</h5>
                    <p>Mulai transaksi penjualan dan terima pembayaran</p>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="{{ route('products.index') }}" class="menu-card text-center">
                    <div class="m-icon" style="background:linear-gradient(135deg,#F59E0B,#FBBF24);color:white;"><i class="bi bi-box-seam"></i></div>
                    <h5>Daftar Produk</h5>
                    <p>Kelola produk, stok, dan harga jual</p>
                    @if($lowStockProducts->count() > 0)
                        <span class="badge mt-1" style="background:#fee2e2;color:#991b1b;border-radius:8px;">{{ $lowStockProducts->count() }} stok menipis</span>
                    @endif
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="{{ route('laporan.index') }}" class="menu-card text-center">
                    <div class="m-icon" style="background:linear-gradient(135deg,#6366F1,#8B5CF6);color:white;"><i class="bi bi-graph-up-arrow"></i></div>
                    <h5>Laporan Keuangan</h5>
                    <p>Dashboard profit, laporan bulanan & export data</p>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="{{ route('users.index') }}" class="menu-card text-center">
                    <div class="m-icon" style="background:linear-gradient(135deg,#06B6D4,#0E7490);color:white;"><i class="bi bi-people-fill"></i></div>
                    <h5>Manajemen User</h5>
                    <p>Kelola akun, role, dan password pengguna</p>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="{{ route('kasir.history') }}" class="menu-card text-center">
                    <div class="m-icon" style="background:linear-gradient(135deg,#EC4899,#DB2777);color:white;"><i class="bi bi-clock-history"></i></div>
                    <h5>Riwayat Transaksi</h5>
                    <p>Lihat semua catatan transaksi yang telah terjadi</p>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="{{ route('settings.index') }}" class="menu-card text-center">
                    <div class="m-icon" style="background:linear-gradient(135deg,#475569,#334155);color:white;"><i class="bi bi-gear-fill"></i></div>
                    <h5>Pengaturan Toko</h5>
                    <p>Atur nama toko, alamat, telepon & struk</p>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="{{ route('admin.activity-logs') }}" class="menu-card text-center">
                    <div class="m-icon" style="background:linear-gradient(135deg,#10B981,#047857);color:white;"><i class="bi bi-shield-check"></i></div>
                    <h5>Audit Trail & Log</h5>
                    <p>Pantau rekaman audit perubahan data & transaksi</p>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <form action="{{ route('admin.clear-cache') }}" method="POST" onsubmit="return confirm('Bersihkan seluruh cache aplikasi sekarang?')" class="h-100">
                    @csrf
                    <button type="submit" class="menu-card text-center w-100 h-100 border-0 bg-white" style="cursor:pointer;">
                        <div class="m-icon" style="background:linear-gradient(135deg,#64748B,#334155);color:white;"><i class="bi bi-arrow-repeat"></i></div>
                        <h5>Bersihkan Cache</h5>
                        <p>Segarkan cache view dan konfigurasi aplikasi</p>
                    </button>
                </form>
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