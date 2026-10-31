<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - Kasir</title>
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
        .page-card{background:white;border-radius:20px;box-shadow:var(--shadow);padding:2rem;}
        .user-avatar{width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;}
        .table-premium thead tr th{background:var(--grad);color:white;font-weight:600;font-size:0.8rem;letter-spacing:0.4px;padding:0.9rem 1rem;border:none;}
        .table-premium thead tr th:first-child{border-radius:12px 0 0 0;}
        .table-premium thead tr th:last-child{border-radius:0 12px 0 0;}
        .table-premium tbody tr{border-bottom:1px solid #f0fdf4;transition:background 0.15s;}
        .table-premium tbody tr:hover{background:#f0fdf4;}
        .table-premium tbody td{padding:0.9rem 1rem;vertical-align:middle;border:none;}
        .badge-admin{background:linear-gradient(135deg,#ef4444,#dc2626);color:white;border-radius:8px;padding:4px 12px;font-size:0.78rem;font-weight:700;}
        .badge-kasir{background:linear-gradient(135deg,#059669,#0D9488);color:white;border-radius:8px;padding:4px 12px;font-size:0.78rem;font-weight:700;}
        .btn-sm-action{border:none;border-radius:9px;padding:5px 11px;font-weight:600;font-size:0.78rem;transition:all 0.2s;cursor:pointer;display:inline-flex;align-items:center;gap:4px;}
        .btn-edit-u{background:#fef3c7;color:#92400e;} .btn-edit-u:hover{background:#fbbf24;color:white;}
        .btn-setpwd{background:#dbeafe;color:#1e40af;} .btn-setpwd:hover{background:#3b82f6;color:white;}
        .btn-reset{background:#ede9fe;color:#5b21b6;} .btn-reset:hover{background:#7c3aed;color:white;}
        .btn-del-u{background:#fee2e2;color:#991b1b;} .btn-del-u:hover{background:#ef4444;color:white;}
        .btn-dis{background:#f1f5f9;color:#94a3b8;cursor:not-allowed;}
        .search-input{border:1.5px solid #e5e7eb;border-radius:10px;padding:0.58rem 1rem 0.58rem 2.3rem;font-size:0.88rem;transition:all 0.2s;width:220px;}
        .search-input:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(5,150,105,0.12);outline:none;}
    </style>
</head>
<body>
    <nav class="app-nav d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn-nav-back"><i class="bi bi-arrow-left"></i> Dashboard</a>
            <div class="brand"><i class="bi bi-people-fill"></i> Manajemen Pengguna</div>
        </div>
        <span style="color:rgba(255,255,255,0.8);font-size:0.85rem;">Kasir</span>
    </nav>

    <div class="container-fluid px-4 py-4">
        @if(session('success'))
            <div class="alert border-0 rounded-3 py-2 mb-3" style="background:#f0fdf4;color:#065f46;font-size:0.9rem;">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 rounded-3 py-2 mb-3" style="font-size:0.9rem;">
                <i class="bi bi-exclamation-circle-fill me-1"></i> {{ session('error') }}
            </div>
        @endif

        <div class="page-card">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h5 style="font-weight:800;margin-bottom:0.1rem;">Daftar Pengguna</h5>
                    <div style="font-size:0.82rem;color:#94a3b8;">{{ count($users) }} akun terdaftar di sistem</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div style="position:relative;">
                        <i class="bi bi-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:0.85rem;"></i>
                        <label for="searchUser" class="visually-hidden">Cari nama atau email pengguna</label>
                        <input type="search" id="searchUser" class="search-input" placeholder="Cari nama/email...">
                    </div>
                    <a href="{{ route('users.create') }}" class="btn btn-success fw-bold text-white px-3" style="border-radius:10px;font-size:0.88rem;">
                        <i class="bi bi-plus-lg me-1"></i> Tambah User Baru
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-premium" style="border-collapse:separate;border-spacing:0;">
                    <thead><tr>
                        <th>#</th><th>Pengguna</th><th>Email</th><th class="text-center">Role</th><th class="text-center">Aksi</th>
                    </tr></thead>
                    <tbody id="bodyUser">
                        @foreach($users as $user)
                        <tr data-search="{{ strtolower($user->name.' '.$user->email) }}">
                            <td style="color:#94a3b8;font-weight:600;">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar" style="background:{{ $user->role==='admin'?'linear-gradient(135deg,#ef4444,#dc2626)':'linear-gradient(135deg,#059669,#0D9488)' }};color:white;">
                                        {{ strtoupper(substr($user->name,0,1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:700;font-size:0.92rem;">{{ $user->name }}</div>
                                        @if($user->id === auth()->user()->id)
                                            <div style="font-size:0.72rem;color:#059669;font-weight:600;">● Anda</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="color:#64748b;font-size:0.9rem;">{{ $user->email }}</td>
                            <td class="text-center">
                                @if($user->role === 'admin')
                                    <span class="badge-admin">Admin</span>
                                @else
                                    <span class="badge-kasir">Kasir</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn-sm-action btn-edit-u"><i class="bi bi-pencil-fill"></i> Edit</a>
                                    <a href="{{ route('users.set-password', $user->id) }}" class="btn-sm-action btn-setpwd"><i class="bi bi-key"></i> Set Pass</a>
                                    <form action="{{ route('users.reset-password', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Kirim tautan reset password ke {{ $user->email }}?')">
                                        @csrf
                                        <button type="submit" class="btn-sm-action btn-reset"><i class="bi bi-envelope"></i> Reset</button>
                                    </form>
                                    @if($user->id !== auth()->user()->id)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-sm-action btn-del-u" aria-label="Hapus pengguna {{ $user->name }}" title="Hapus pengguna"><i class="bi bi-trash-fill" aria-hidden="true"></i></button>
                                    </form>
                                    @else
                                    <span class="btn-sm-action btn-dis" aria-label="Tidak dapat menghapus akun sendiri" title="Tidak dapat menghapus akun sendiri"><i class="bi bi-trash-fill" aria-hidden="true"></i></span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @if($users->isEmpty())
                        <tr><td colspan="5" class="text-center py-5" style="color:#94a3b8;">Belum ada pengguna terdaftar.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('searchUser').addEventListener('input', function() {
            const kw = this.value.toLowerCase();
            document.querySelectorAll('#bodyUser tr[data-search]').forEach(r => {
                r.style.display = r.getAttribute('data-search').includes(kw) ? '' : 'none';
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
