<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas & Audit Trail - {{ \App\Models\StoreSetting::getSetting()->store_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family:'Inter',sans-serif; }
        :root {
            --primary:#059669; --accent:#0D9488;
            --grad:linear-gradient(135deg,#059669 0%,#0D9488 100%);
            --bg:#f0fdf4; --shadow:0 4px 20px rgba(5,150,105,0.10);
        }
        body { background:var(--bg); color:#1e293b; }
        .app-nav { background:var(--grad); padding:0.9rem 1.75rem; box-shadow:0 4px 24px rgba(5,150,105,0.28); }
        .brand { color:white; font-weight:800; font-size:1.15rem; display:flex; align-items:center; gap:0.6rem; }
        .btn-nav-back { background:rgba(255,255,255,0.15); border:1.5px solid rgba(255,255,255,0.3); color:white; border-radius:10px; padding:0.4rem 1rem; font-weight:600; font-size:0.88rem; text-decoration:none; transition:all 0.2s; display:inline-flex; align-items:center; gap:0.4rem; }
        .btn-nav-back:hover { background:rgba(255,255,255,0.25); color:white; }
        .page-card { background:white; border-radius:20px; box-shadow:var(--shadow); padding:1.75rem; }
        .table-premium thead tr th { background:var(--grad); color:white; font-weight:600; font-size:0.82rem; padding:0.85rem 1rem; border:none; }
        .table-premium thead tr th:first-child { border-radius:12px 0 0 0; }
        .table-premium thead tr th:last-child { border-radius:0 12px 0 0; }
        .table-premium tbody tr { border-bottom:1px solid #f0fdf4; transition:background 0.15s; }
        .table-premium tbody tr:hover { background:#f8fafc; }
        .table-premium tbody td { padding:0.85rem 1rem; vertical-align:middle; border:none; font-size:0.88rem; }
        .badge-action { font-size:0.75rem; font-weight:700; padding:4px 9px; border-radius:8px; text-transform:uppercase; letter-spacing:0.3px; }
        .badge-login { background:#e0f2fe; color:#0369a1; }
        .badge-logout { background:#f1f5f9; color:#475569; }
        .badge-transaksi { background:#dcfce7; color:#15803d; }
        .badge-void { background:#fee2e2; color:#b91c1c; }
        .badge-security { background:#fef3c7; color:#b45309; }
        .badge-default { background:#f3e8ff; color:#7e22ce; }
        .search-box { border:1.5px solid #e5e7eb; border-radius:10px; padding:0.5rem 0.9rem; font-size:0.88rem; }
        .search-box:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(5,150,105,0.12); outline:none; }
        .pagination { --bs-pagination-color:var(--primary); --bs-pagination-hover-color:#047857; --bs-pagination-focus-color:#047857; --bs-pagination-active-bg:var(--primary); --bs-pagination-active-border-color:var(--primary); --bs-pagination-focus-box-shadow:0 0 0 0.2rem rgba(5,150,105,0.15); }
        .pagination .page-link { color:var(--primary); border-color:#d1fae5; }
        .pagination .page-link:hover { color:white; background:#10b981; border-color:#10b981; }
        .pagination .page-item.active .page-link { color:white; background:var(--primary); border-color:var(--primary); }
        .pagination .page-item.disabled .page-link { color:#94a3b8; background:#f8fafc; border-color:#e5e7eb; }
    </style>
</head>
<body>
    <nav class="app-nav d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn-nav-back"><i class="bi bi-arrow-left"></i> Dashboard</a>
            <div class="brand"><i class="bi bi-shield-check"></i> Audit Trail & Log Aktivitas</div>
        </div>
        <div class="text-white small">
            <i class="bi bi-person-check me-1"></i> Admin: <strong>{{ auth()->user()->name }}</strong>
        </div>
    </nav>

    <div class="container-fluid px-4 py-4">
        <div class="page-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h4 style="font-weight:800;color:#1e293b;margin:0;">Catatan Jejak Audit Sistem</h4>
                    <p style="color:#64748b;margin:0;font-size:0.88rem;">Setiap perubahan transaksi, pembatalan nota, pengelolaan produk, dan aktivitas user tercatat otomatis di sini.</p>
                </div>
                <form method="GET" action="{{ route('admin.activity-logs') }}" class="d-flex gap-2">
                    <select name="action" class="search-box" onchange="this.form.submit()">
                        <option value="">-- Semua Aksi --</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ strtoupper($act) }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="search" class="search-box" placeholder="Cari deskripsi..." value="{{ request('search') }}">
                    <input type="date" name="date" class="search-box" aria-label="Filter tanggal audit" value="{{ request('date') }}">
                    <button type="submit" class="btn btn-sm text-white px-3 fw-bold" style="background:var(--primary);border-radius:10px;">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request()->hasAny(['action', 'search', 'date']))
                        <a href="{{ route('admin.activity-logs') }}" class="btn btn-sm btn-light border fw-semibold" style="border-radius:10px;">Reset</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-premium w-100">
                    <thead>
                        <tr>
                            <th style="width:170px;">Waktu</th>
                            <th style="width:150px;">Pengguna</th>
                            <th style="width:160px;">Kategori Aksi</th>
                            <th>Deskripsi Aktivitas</th>
                            <th style="width:130px;">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        @php
                            $actionClass = match(true) {
                                str_contains($log->action, 'login') => 'badge-login',
                                str_contains($log->action, 'logout') => 'badge-logout',
                                str_contains($log->action, 'transaksi') => 'badge-transaksi',
                                str_contains($log->action, 'void') => 'badge-void',
                                str_contains($log->action, 'password') || str_contains($log->action, 'user') => 'badge-security',
                                default => 'badge-default',
                            };
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight:700;">{{ $log->created_at->format('d M Y') }}</div>
                                <div style="font-size:0.78rem;color:#64748b;">{{ $log->created_at->format('H:i:s') }} ({{ $log->created_at->diffForHumans() }})</div>
                            </td>
                            <td>
                                @if($log->user)
                                    <div style="font-weight:700;color:#1e293b;"><i class="bi bi-person-circle me-1 text-success"></i>{{ $log->user->name }}</div>
                                    <div style="font-size:0.75rem;color:#94a3b8;">{{ $log->user->role }}</div>
                                @else
                                    <span class="text-muted"><i class="bi bi-robot me-1"></i>Sistem / Tamu</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-action {{ $actionClass }}">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>
                            <td style="color:#334155;line-height:1.45;">
                                {{ $log->description }}
                            </td>
                            <td style="color:#64748b;font-family:monospace;font-size:0.82rem;">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i> Belum ada aktivitas yang tercatat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
