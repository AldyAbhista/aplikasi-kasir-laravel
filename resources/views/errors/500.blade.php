<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Terjadi Kesalahan Sistem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f0fdf4; margin: 0; padding: 1rem; }
        .error-card { background: white; border-radius: 24px; padding: 3rem 2rem; max-width: 480px; width: 100%; text-align: center; box-shadow: 0 10px 40px rgba(5,150,105,0.12); }
        .error-code { font-size: 5rem; font-weight: 800; color: #ea580c; line-height: 1; margin-bottom: 1rem; }
        .btn-home { background: linear-gradient(135deg,#059669 0%,#0D9488 100%); color: white; border: none; border-radius: 12px; padding: 0.8rem 1.6rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s; }
        .btn-home:hover { opacity: 0.92; color: white; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="error-card">
        <div style="width:72px;height:72px;background:#ffedd5;color:#ea580c;border-radius:20px;display:inline-flex;align-items:center;justify-content:center;font-size:2.2rem;margin-bottom:1.5rem;">
            <i class="bi bi-exclamation-octagon-fill"></i>
        </div>
        <div class="error-code">500</div>
        <h4 style="font-weight:800;color:#1e293b;">Kendala Server</h4>
        <p style="color:#64748b;font-size:0.95rem;margin-bottom:2rem;">
            Terjadi sedikit kendala internal pada server. Tim sistem sedang menangani masalah ini.
        </p>
        <a href="{{ url('/') }}" class="btn-home">
            <i class="bi bi-arrow-clockwise"></i> Muat Ulang Halaman
        </a>
    </div>
</body>
</html>
