<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password - Kasir</title>
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
        .tip-item { display:flex; align-items:center; gap:0.7rem; color:rgba(255,255,255,0.9); font-size:0.88rem; margin-bottom:0.8rem; }
        .tip-item i { background:rgba(255,255,255,0.2); padding:5px 7px; border-radius:8px; }
        .auth-right { flex:1; display:flex; align-items:center; justify-content:center; padding:2rem; background:white; }
        .auth-form-box { width:100%; max-width:420px; }
        .form-label { font-weight:600; font-size:0.875rem; color:#374151; margin-bottom:0.4rem; }
        .form-control { border:1.5px solid #e5e7eb; border-radius:10px; padding:0.75rem 1rem; transition:all 0.2s; }
        .form-control:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(5,150,105,0.12); }
        .btn-auth { background:var(--grad); border:none; color:white; border-radius:12px; padding:0.85rem; font-weight:700; font-size:0.97rem; width:100%; transition:all 0.3s; }
        .btn-auth:hover { opacity:0.92; transform:translateY(-1px); box-shadow:0 8px 25px rgba(5,150,105,0.35); color:white; }
        .link-green { color:var(--primary); font-weight:600; text-decoration:none; }
        @media (max-width:768px) { .auth-left { display:none; } }
    </style>
</head>
<body>
    <div class="auth-left">
        <div class="brand-icon"><i class="bi bi-lock-fill"></i></div>
        <h2 style="color:white;font-weight:800;text-align:center;margin-bottom:0.5rem;">Keamanan Akun</h2>
        <p style="color:rgba(255,255,255,0.8);text-align:center;font-size:0.93rem;margin-bottom:2rem;">Tips membuat password yang kuat:</p>
        <div class="tip-item"><i class="bi bi-check-lg"></i> Minimal 6 karakter</div>
        <div class="tip-item"><i class="bi bi-check-lg"></i> Campurkan huruf & angka</div>
        <div class="tip-item"><i class="bi bi-check-lg"></i> Jangan gunakan tanggal lahir</div>
        <div class="tip-item"><i class="bi bi-check-lg"></i> Jangan bagikan ke orang lain</div>
    </div>
    <div class="auth-right">
        <div class="auth-form-box">
            <div class="mb-4">
                <div style="width:52px;height:52px;background:#f0fdf4;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                    <i class="bi bi-shield-lock-fill" style="font-size:1.5rem;color:var(--primary);"></i>
                </div>
                <h2 style="font-weight:800;color:#1e293b;">Ganti Password</h2>
                <p style="color:#6b7280;margin:0;font-size:0.93rem;">Perbarui password akun Anda</p>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger border-0 rounded-3 py-2 mb-3" style="font-size:0.88rem;">
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul>
                </div>
            @endif
            @if (session('success'))
                <div class="alert border-0 rounded-3 py-2 mb-3" style="background:#f0fdf4;color:#065f46;font-size:0.9rem;">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                </div>
            @endif
            <form action="{{ route('change-password.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Password Lama</label>
                    <input type="password" name="password_lama" class="form-control" placeholder="Masukkan password lama" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password_baru" id="pwdBaru" class="form-control" placeholder="Min. 6 karakter" required minlength="6" style="border-right:none;">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('pwdBaru','iconBaru')" tabindex="-1" style="border-left:none;border-radius:0 10px 10px 0;border-color:#e5e7eb;"><i class="bi bi-eye" id="iconBaru"></i></button>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password_baru_confirmation" id="pwdKonfirm" class="form-control" placeholder="Ulangi password baru" required minlength="6" style="border-right:none;">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('pwdKonfirm','iconKonfirm')" tabindex="-1" style="border-left:none;border-radius:0 10px 10px 0;border-color:#e5e7eb;"><i class="bi bi-eye" id="iconKonfirm"></i></button>
                    </div>
                </div>
                <button type="submit" class="btn-auth mb-3">Perbarui Password</button>
                <div class="text-center">
                    <a href="javascript:history.back()" class="link-green" style="font-size:0.9rem;"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        function togglePwd(id, iconId) {
            const el = document.getElementById(id);
            const icon = document.getElementById(iconId);
            el.type = el.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('bi-eye'); icon.classList.toggle('bi-eye-slash');
        }
    </script>
</body>
</html>
