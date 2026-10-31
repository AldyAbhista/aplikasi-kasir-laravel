<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        :root {
            --primary: #059669; --accent: #0D9488;
            --grad: linear-gradient(135deg, #059669 0%, #0D9488 100%);
        }
        body { margin: 0; min-height: 100vh; display: flex; background: #f0fdf4; }
        /* Left Panel */
        .auth-left {
            width: 42%; background: var(--grad);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 3rem 2.5rem; position: relative; overflow: hidden;
        }
        .auth-left::before {
            content: ''; position: absolute; top: -80px; right: -80px;
            width: 280px; height: 280px; border-radius: 50%;
            background: rgba(255,255,255,0.08);
        }
        .auth-left::after {
            content: ''; position: absolute; bottom: -60px; left: -60px;
            width: 200px; height: 200px; border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }
        .brand-icon {
            width: 90px; height: 90px; background: rgba(255,255,255,0.2);
            border-radius: 24px; display: flex; align-items: center; justify-content: center;
            font-size: 2.8rem; color: white; margin-bottom: 1.5rem;
            backdrop-filter: blur(10px); border: 2px solid rgba(255,255,255,0.3);
        }
        .brand-title { color: white; font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; }
        .brand-sub { color: rgba(255,255,255,0.8); font-size: 1rem; text-align: center; }
        .feature-list { list-style: none; padding: 0; margin-top: 2.5rem; width: 100%; }
        .feature-list li {
            color: rgba(255,255,255,0.9); padding: 0.6rem 0;
            display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem;
        }
        .feature-list li i { background: rgba(255,255,255,0.2); padding: 5px; border-radius: 8px; }
        /* Right Panel */
        .auth-right {
            flex: 1; display: flex; align-items: center; justify-content: center;
            padding: 2rem; background: white;
        }
        .auth-form-box { width: 100%; max-width: 420px; }
        .form-label { font-weight: 600; font-size: 0.875rem; color: #374151; margin-bottom: 0.4rem; }
        .form-control {
            border: 1.5px solid #e5e7eb; border-radius: 10px;
            padding: 0.75rem 1rem; font-size: 0.95rem; transition: all 0.2s;
        }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(5,150,105,0.12); }
        .btn-auth {
            background: var(--grad); border: none; color: white;
            border-radius: 12px; padding: 0.85rem; font-weight: 700;
            font-size: 1rem; width: 100%; transition: all 0.3s;
        }
        .btn-auth:hover { opacity: 0.92; transform: translateY(-1px); box-shadow: 0 8px 25px rgba(5,150,105,0.35); color:white; }
        .link-green { color: var(--primary); font-weight: 600; text-decoration: none; }
        .link-green:hover { text-decoration: underline; }
        .divider-text { color: #9ca3af; font-size: 0.85rem; margin: 1.25rem 0; text-align: center; }
        @media (max-width: 768px) { .auth-left { display: none; } .auth-right { background: #f0fdf4; } }
    </style>
</head>
<body>
    <!-- Left Panel -->
    <div class="auth-left">
        <div class="brand-icon"><i class="bi bi-shop"></i></div>
        <div class="brand-title">{{ \App\Models\StoreSetting::getSetting()->store_name }}</div>
        <div class="brand-sub">Sistem Kasir Modern & Terpercaya</div>
        <ul class="feature-list">
            <li><i class="bi bi-lightning-charge-fill"></i> Transaksi cepat & akurat</li>
            <li><i class="bi bi-graph-up-arrow"></i> Laporan keuangan real-time</li>
            <li><i class="bi bi-shield-check"></i> Data aman & terenkripsi</li>
            <li><i class="bi bi-printer-fill"></i> Cetak struk otomatis</li>
        </ul>
    </div>

    <!-- Right Panel -->
    <div class="auth-right">
        <div class="auth-form-box">
            <div class="mb-4">
                <h2 style="font-weight:800;color:#1e293b;">Selamat Datang 👋</h2>
                <p style="color:#6b7280;margin:0;">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success border-0 rounded-3 py-2 mb-3" style="background:#f0fdf4;color:#065f46;font-size:0.9rem;">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger border-0 rounded-3 py-2 mb-3" style="font-size:0.9rem;">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="nama@email.com" required value="{{ old('email') }}">
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required style="border-right:none;">
                        <button type="button" class="btn btn-outline-secondary" id="togglePwd" tabindex="-1" style="border-left:none;border-radius:0 10px 10px 0;border-color:#e5e7eb;">
                            <i class="bi bi-eye" id="togglePwdIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-auth mb-3">Masuk</button>

                <div class="d-flex justify-content-between align-items-center" style="font-size:0.88rem;">
                    <a href="{{ route('forgot-password') }}" class="link-green">
                        <i class="bi bi-question-circle"></i> Lupa Password?
                    </a>
                    <span class="text-muted text-end" style="max-width:220px;">
                        <i class="bi bi-info-circle"></i> Akun kasir dibuat oleh admin toko.
                    </span>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('togglePwd').addEventListener('click', function() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('togglePwdIcon');
            pwd.type = pwd.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>