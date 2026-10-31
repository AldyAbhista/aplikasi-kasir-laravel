<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna - Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{font-family:'Inter',sans-serif;}
        :root{--primary:#059669;--grad:linear-gradient(135deg,#059669 0%,#0D9488 100%);}
        body{background:#f0fdf4;color:#1e293b;}
        .app-nav{background:var(--grad);padding:0.9rem 1.75rem;box-shadow:0 4px 24px rgba(5,150,105,0.28);}
        .brand{color:white;font-weight:800;font-size:1.15rem;display:flex;align-items:center;gap:0.6rem;}
        .btn-nav-back{background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:white;border-radius:10px;padding:0.4rem 1rem;font-weight:600;font-size:0.88rem;text-decoration:none;transition:all 0.2s;display:inline-flex;align-items:center;gap:0.4rem;}
        .btn-nav-back:hover{background:rgba(255,255,255,0.25);color:white;}
        .form-card{background:white;border-radius:20px;box-shadow:0 4px 20px rgba(5,150,105,0.10);padding:2rem;}
        .form-label{font-weight:600;font-size:0.875rem;color:#374151;margin-bottom:0.4rem;}
        .form-control,.form-select{border:1.5px solid #e5e7eb;border-radius:10px;padding:0.7rem 1rem;transition:all 0.2s;}
        .form-control:focus,.form-select:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(5,150,105,0.12);}
        .form-control:disabled{background:#f8fafc;color:#94a3b8;}
        .btn-submit{background:var(--grad);border:none;color:white;border-radius:12px;padding:0.82rem;font-weight:700;width:100%;transition:all 0.3s;}
        .btn-submit:hover{opacity:0.92;transform:translateY(-1px);box-shadow:0 8px 25px rgba(5,150,105,0.35);color:white;}
        .btn-cancel{background:#f1f5f9;border:none;color:#475569;border-radius:12px;padding:0.82rem;font-weight:600;width:100%;text-decoration:none;display:block;text-align:center;transition:all 0.2s;}
        .btn-cancel:hover{background:#e2e8f0;color:#334155;}
        .user-banner{background:var(--grad);border-radius:16px;padding:1.25rem;margin-bottom:1.5rem;color:white;display:flex;align-items:center;gap:1rem;}
        .user-ava{width:56px;height:56px;background:rgba(255,255,255,0.2);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:800;border:2px solid rgba(255,255,255,0.3);}
    </style>
</head>
<body>
    <nav class="app-nav d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('users.index') }}" class="btn-nav-back"><i class="bi bi-arrow-left"></i> Pengguna</a>
            <div class="brand"><i class="bi bi-pencil-square"></i> Edit Pengguna</div>
        </div>
        <span style="color:rgba(255,255,255,0.8);font-size:0.85rem;">Kasir</span>
    </nav>

    <div class="container py-4" style="max-width:560px;">
        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3 py-2 mb-3" style="font-size:0.88rem;">
                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul>
            </div>
        @endif

        <div class="form-card">
            <div class="user-banner">
                <div class="user-ava">{{ strtoupper(substr($user->name,0,1)) }}</div>
                <div>
                    <div style="font-weight:800;font-size:1.05rem;">{{ $user->name }}</div>
                    <div style="opacity:0.8;font-size:0.85rem;">{{ $user->email }}</div>
                    <div style="margin-top:4px;background:rgba(255,255,255,0.2);border-radius:8px;padding:2px 10px;display:inline-block;font-size:0.78rem;font-weight:700;">
                        {{ ucfirst($user->role) }}
                    </div>
                </div>
            </div>

            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">ID Pengguna</label>
                    <input type="text" class="form-control" value="#{{ $displayId }}" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Role / Posisi</label>
                    <select name="role" class="form-select" required>
                        <option value="kasir" {{ $user->role==='kasir'?'selected':'' }}>👤 Pegawai Kasir</option>
                        <option value="admin" {{ $user->role==='admin'?'selected':'' }}>🛡️ Pemilik / Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit mb-2"><i class="bi bi-check-circle me-1"></i> Simpan Perubahan</button>
                <a href="{{ route('users.index') }}" class="btn-cancel"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
