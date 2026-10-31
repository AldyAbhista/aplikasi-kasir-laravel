<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - {{ \App\Models\StoreSetting::getSetting()->store_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family:'Inter',sans-serif; }
        :root { --primary:#059669; --grad:linear-gradient(135deg,#059669 0%,#0D9488 100%); }
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; background:#f0fdf4; padding:1.5rem; }
        .verify-card { background:white; border-radius:24px; box-shadow:0 10px 40px rgba(5,150,105,0.12); width:100%; max-width:480px; padding:2.5rem; text-align:center; }
        .verify-icon { width:80px; height:80px; background:#dcfce7; color:var(--primary); border-radius:24px; display:inline-flex; align-items:center; justify-content:center; font-size:2.4rem; margin-bottom:1.5rem; }
        .btn-auth { background:var(--grad); border:none; color:white; border-radius:12px; padding:0.8rem 1.5rem; font-weight:700; font-size:0.95rem; width:100%; transition:all 0.3s; }
        .btn-auth:hover { opacity:0.92; transform:translateY(-1px); box-shadow:0 8px 25px rgba(5,150,105,0.3); color:white; }
        .btn-logout { background:#f1f5f9; border:none; color:#475569; border-radius:12px; padding:0.75rem 1.5rem; font-weight:600; font-size:0.92rem; width:100%; transition:all 0.2s; }
        .btn-logout:hover { background:#e2e8f0; color:#1e293b; }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="verify-icon">
            <i class="bi bi-envelope-check"></i>
        </div>
        <h3 style="font-weight:800;color:#1e293b;margin-bottom:0.75rem;">Verifikasi Email Anda</h3>
        <p style="color:#64748b;font-size:0.93rem;line-height:1.6;margin-bottom:1.5rem;">
            Terima kasih telah mendaftar! Tautan verifikasi telah kami kirimkan ke alamat email <strong>{{ auth()->user()?->email }}</strong>.
            Silakan buka email Anda dan klik tombol verifikasi untuk mengaktifkan akun kasir Anda sepenuhnya.
        </p>

        @if (session('success'))
            <div class="alert border-0 rounded-3 py-2 mb-3 text-start" style="background:#f0fdf4;color:#065f46;font-size:0.88rem;">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            </div>
        @endif

        <div class="d-flex flex-column gap-2 mb-4">
            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button type="submit" class="btn-auth">
                    <i class="bi bi-arrow-clockwise me-1"></i> Kirim Ulang Email Verifikasi
                </button>
            </form>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-box-arrow-right me-1"></i> Keluar / Ganti Akun
                </button>
            </form>
        </div>

        <div style="font-size:0.8rem;color:#94a3b8;">
            <i class="bi bi-info-circle me-1"></i> Tidak menerima email? Cek folder Spam atau tunggu beberapa saat. (Pada server pengujian lokal, tautan verifikasi tercatat di file <code>storage/logs/laravel.log</code>).
        </div>
    </div>
</body>
</html>
