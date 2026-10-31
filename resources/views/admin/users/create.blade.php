<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User Baru - Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{font-family:'Inter',sans-serif;}
        :root{--primary:#059669;--grad:linear-gradient(135deg,#059669 0%,#0D9488 100%);--bg:#f0fdf4;--shadow:0 4px 20px rgba(5,150,105,0.10);}
        body{background:var(--bg);color:#1e293b;}
        .app-nav{background:var(--grad);padding:0.9rem 1.75rem;box-shadow:0 4px 24px rgba(5,150,105,0.28);}
        .brand{color:white;font-weight:800;font-size:1.15rem;display:flex;align-items:center;gap:0.6rem;}
        .btn-nav-back{background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:white;border-radius:10px;padding:0.4rem 1rem;font-weight:600;font-size:0.88rem;text-decoration:none;transition:all 0.2s;display:inline-flex;align-items:center;gap:0.4rem;}
        .btn-nav-back:hover{background:rgba(255,255,255,0.25);color:white;}
        .form-card{background:white;border-radius:20px;box-shadow:var(--shadow);padding:2.25rem;}
        .form-control:focus, .form-select:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(5,150,105,0.12);}
        .btn-submit{background:var(--grad);border:none;color:white;border-radius:12px;padding:0.75rem 2rem;font-weight:700;transition:all 0.3s;}
        .btn-submit:hover{opacity:0.9;transform:translateY(-1px);color:white;}
    </style>
</head>
<body>
    <nav class="app-nav d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('users.index') }}" class="btn-nav-back"><i class="bi bi-arrow-left"></i> Kembali</a>
            <div class="brand"><i class="bi bi-person-plus-fill"></i> Tambah User Baru</div>
        </div>
    </nav>

    <div class="container py-4" style="max-width:580px;">
        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-card">
            <h5 class="fw-bold mb-4"><i class="bi bi-person-badge me-2" style="color:var(--primary);"></i> Form Pengguna Baru</h5>

            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control form-control-lg" placeholder="Contoh: Ahmad Kasir" required value="{{ old('name') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Alamat Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="contoh: ahmad@kasir.com" required value="{{ old('email') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Role / Hak Akses <span class="text-danger">*</span></label>
                    <select name="role" class="form-select form-select-lg" required>
                        <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>Kasir (Akses Transaksi & Riwayat Kasir)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Akses Penuh Semua Menu)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Minimal 8 karakter" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control form-control-lg" placeholder="Ulangi password di atas" required>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-lg" style="border-radius:12px;">Batal</a>
                    <button type="submit" class="btn-submit btn-lg">
                        <i class="bi bi-check-circle me-1"></i> Simpan User Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
