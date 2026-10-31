<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir - Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{font-family:'Inter',sans-serif;}
        :root{--primary:#059669;--grad:linear-gradient(135deg,#059669 0%,#0D9488 100%);--bg:#f0fdf4;--shadow:0 4px 20px rgba(5,150,105,0.10);--shadow-h:0 12px 40px rgba(5,150,105,0.20);}
        body{background:var(--bg);color:#1e293b;}
        .app-nav{background:var(--grad);padding:0.9rem 1.75rem;box-shadow:0 4px 24px rgba(5,150,105,0.28);}
        .app-nav .brand{color:white;font-weight:800;font-size:1.2rem;display:flex;align-items:center;gap:0.6rem;}
        .btn-nav-out{background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:white;border-radius:10px;padding:0.4rem 1rem;font-weight:600;font-size:0.88rem;transition:all 0.2s;}
        .btn-nav-out:hover{background:rgba(255,100,100,0.25);border-color:rgba(255,150,150,0.5);color:white;}
        .stat-card{background:white;border-radius:18px;padding:1.4rem 1.5rem;box-shadow:var(--shadow);border:none;transition:all 0.3s;}
        .stat-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-h);}
        .stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;margin-bottom:0.8rem;}
        .stat-label{font-size:0.78rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;}
        .stat-value{font-size:1.6rem;font-weight:800;line-height:1.1;margin:0.2rem 0;}
        .stat-sub{font-size:0.77rem;color:#94a3b8;}
        .menu-card{background:white;border-radius:18px;padding:2rem 1.5rem;box-shadow:var(--shadow);text-decoration:none;color:#1e293b;display:block;transition:all 0.3s;border:2px solid transparent;text-align:center;}
        .menu-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-h);border-color:#a7f3d0;color:#1e293b;}
        .m-icon{width:72px;height:72px;border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:2.1rem;margin:0 auto 1.1rem;}
        .menu-card h5{font-weight:700;font-size:1rem;margin-bottom:0.35rem;}
        .menu-card p{font-size:0.83rem;color:#64748b;margin:0;}
        .greeting-card{background:var(--grad);border-radius:20px;padding:1.75rem 2rem;color:white;margin-bottom:1.5rem;position:relative;overflow:hidden;}
        .greeting-card::before{content:'';position:absolute;top:-40px;right:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,0.08);}
        .greeting-card::after{content:'';position:absolute;bottom:-30px;left:-30px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.06);}
    </style>
</head>
<body>
    @php
        $storeName = \App\Models\StoreSetting::getSetting()->store_name ?? 'Kasir';
    @endphp
    <nav class="app-nav d-flex align-items-center justify-content-between">
        <div class="brand">
            <div style="width:36px;height:36px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-shop" style="color:white;font-size:1.2rem;"></i>
            </div>
            {{ $storeName }} &nbsp;<span style="opacity:0.6;font-weight:400;font-size:0.95rem;">/ Kasir</span>
        </div>
        @if(auth()->user()?->role === 'kasir')
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn-nav-out"><i class="bi bi-box-arrow-right me-1"></i>Keluar</button>
        </form>
        @endif
    </nav>

    <div class="container py-4" style="max-width:900px;">
        @php
            $todayRevenue = \App\Models\Transaction::whereDate('created_at', today())->sum('total_harga');
            $todayTrx     = \App\Models\Transaction::whereDate('created_at', today())->count();
            $totalTrx     = \App\Models\Transaction::count();
        @endphp

        <!-- Greeting -->
        <div class="greeting-card">
            <div style="position:relative;z-index:1;">
                <div style="font-size:0.85rem;opacity:0.8;margin-bottom:0.3rem;">Selamat bekerja, 👋</div>
                <h3 style="font-weight:800;margin-bottom:0.5rem;">{{ auth()->user()->name }}</h3>
                <div style="font-size:0.9rem;opacity:0.85;">{{ now()->isoFormat('dddd, D MMMM Y') }}</div>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#f0fdf4;color:#059669;"><i class="bi bi-receipt-cutoff"></i></div>
                    <div class="stat-label">Transaksi Hari Ini</div>
                    <div class="stat-value text-success">{{ $todayTrx }}</div>
                    <div class="stat-sub">transaksi toko hari ini</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#f0fdfa;color:#0D9488;"><i class="bi bi-cash-coin"></i></div>
                    <div class="stat-label">Penjualan Hari Ini</div>
                    <div class="stat-value" style="color:#0D9488;font-size:1.3rem;">Rp {{ number_format($todayRevenue,0,',','.') }}</div>
                    <div class="stat-sub">total penjualan toko hari ini</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#faf5ff;color:#8B5CF6;"><i class="bi bi-graph-up"></i></div>
                    <div class="stat-label">Total Transaksi Toko</div>
                    <div class="stat-value" style="color:#8B5CF6;">{{ $totalTrx }}</div>
                    <div class="stat-sub">semua waktu</div>
                </div>
            </div>
        </div>

        <!-- Menu -->
        <div class="row g-3">
            <div class="col-md-4">
                <a href="{{ route('kasir.index') }}" class="menu-card">
                    <div class="m-icon" style="background:linear-gradient(135deg,#059669,#0D9488);color:white;"><i class="bi bi-shop"></i></div>
                    <h5>Mulai Transaksi</h5>
                    <p>Buka mesin kasir untuk melayani pelanggan</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('kasir.history') }}" class="menu-card">
                    <div class="m-icon" style="background:linear-gradient(135deg,#6366F1,#8B5CF6);color:white;"><i class="bi bi-clock-history"></i></div>
                    <h5>Riwayat Transaksi</h5>
                    <p>Lihat catatan transaksi yang Anda proses</p>
                    @if($todayTrx > 0)
                        <span class="badge mt-1" style="background:#dcfce7;color:#166534;border-radius:8px;">{{ $todayTrx }} hari ini</span>
                    @endif
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('change-password') }}" class="menu-card">
                    <div class="m-icon" style="background:linear-gradient(135deg,#64748b,#475569);color:white;"><i class="bi bi-key-fill"></i></div>
                    <h5>Ganti Password</h5>
                    <p>Ubah password akun Anda</p>
                </a>
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
