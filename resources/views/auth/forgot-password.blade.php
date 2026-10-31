<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - {{ \App\Models\StoreSetting::getSetting()->store_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family:'Inter',sans-serif; }
        :root { --primary:#059669; --grad:linear-gradient(135deg,#059669 0%,#0D9488 100%); }
        body { margin:0; min-height:100vh; display:flex; background:#f0fdf4; }
        .auth-left { width:42%; background:var(--grad); display:flex; flex-direction:column; align-items:center; justify-content:center; padding:3rem 2.5rem; position:relative; overflow:hidden; }
        .auth-left::before { content:''; position:absolute; top:-80px; right:-80px; width:260px; height:260px; border-radius:50%; background:rgba(255,255,255,0.08); }
        .auth-left::after { content:''; position:absolute; bottom:-60px; left:-60px; width:190px; height:190px; border-radius:50%; background:rgba(255,255,255,0.06); }
        .brand-icon { width:88px; height:88px; background:rgba(255,255,255,0.2); border-radius:22px; display:flex; align-items:center; justify-content:center; font-size:2.6rem; color:white; margin-bottom:1.4rem; border:2px solid rgba(255,255,255,0.3); }
        .auth-right { flex:1; display:flex; align-items:center; justify-content:center; padding:2rem; background:white; }
        .auth-form-box { width:100%; max-width:420px; }
        .form-label { font-weight:600; font-size:0.875rem; color:#374151; margin-bottom:0.4rem; }
        .form-control { border:1.5px solid #e5e7eb; border-radius:10px; padding:0.75rem 1rem; transition:all 0.2s; }
        .form-control:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(5,150,105,0.12); }
        .btn-auth { background:var(--grad); border:none; color:white; border-radius:12px; padding:0.85rem; font-weight:700; font-size:0.97rem; width:100%; transition:all 0.3s; }
        .btn-auth:hover { opacity:0.92; transform:translateY(-1px); box-shadow:0 8px 25px rgba(5,150,105,0.35); color:white; }
        .link-green { color:var(--primary); font-weight:600; text-decoration:none; }
        .link-green:hover { text-decoration:underline; }
        @media (max-width:768px) { .auth-left { display:none; } }
    </style>
</head>
<body>
    <div class="auth-left">
        <div class="brand-icon"><i class="bi bi-shield-lock"></i></div>
        <h2 style="color:white;font-weight:800;text-align:center;">Lupa Password?</h2>
        <p style="color:rgba(255,255,255,0.85);text-align:center;font-size:0.95rem;max-width:340px;">Kami akan mengirimkan tautan verifikasi aman ke email Anda untuk menyetel ulang password.</p>
    </div>
    <div class="auth-right">
        <div class="auth-form-box">
            <div class="mb-4">
                <div style="width:52px;height:52px;background:#f0fdf4;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                    <i class="bi bi-envelope-paper-heart-fill" style="font-size:1.5rem;color:var(--primary);"></i>
                </div>
                <h2 style="font-weight:800;color:#1e293b;">Kirim Link Reset</h2>
                <p style="color:#6b7280;margin:0;font-size:0.93rem;">Masukkan email terdaftar untuk menerima link verifikasi</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger border-0 rounded-3 py-2 mb-3" style="font-size:0.9rem;">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert border-0 rounded-3 py-2 mb-3" style="background:#f0fdf4;color:#065f46;font-size:0.9rem;">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('forgot-password.post') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Email Terdaftar</label>
                    <input type="email" name="email" class="form-control" placeholder="nama@email.com" required value="{{ old('email') }}" autofocus>
                </div>
                <button type="submit" class="btn-auth mb-3">
                    <i class="bi bi-send-fill me-1"></i> Kirim Tautan Reset Password
                </button>
                <div class="text-center">
                    <a href="{{ route('login') }}" class="link-green" style="font-size:0.9rem;">
                        <i class="bi bi-arrow-left"></i> Kembali ke Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
