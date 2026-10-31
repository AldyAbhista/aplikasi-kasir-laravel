<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Toko - Kasir</title>
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
        .page-card{background:white;border-radius:20px;box-shadow:var(--shadow);padding:2.25rem;}
        .form-control:focus, .form-select:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(5,150,105,0.12);}
        .btn-save{background:var(--grad);border:none;color:white;border-radius:12px;padding:0.75rem 2rem;font-weight:700;transition:all 0.3s;}
        .btn-save:hover{opacity:0.9;transform:translateY(-1px);color:white;}
    </style>
</head>
<body>
    <nav class="app-nav d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn-nav-back"><i class="bi bi-arrow-left"></i> Dashboard</a>
            <div class="brand"><i class="bi bi-gear-fill"></i> Pengaturan Toko & Struk</div>
        </div>
        <span style="color:rgba(255,255,255,0.8);font-size:0.85rem;">Kasir</span>
    </nav>

    <div class="container py-4" style="max-width:680px;">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <div class="page-card">
            <h5 class="fw-bold mb-4"><i class="bi bi-shop me-2" style="color:var(--primary);"></i> Identitas Toko & Cetakan Struk</h5>
            
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Toko / Usaha <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shop"></i></span>
                        <input type="text" name="store_name" class="form-control form-control-lg" value="{{ old('store_name', $setting->store_name) }}" required placeholder="Contoh: Toko Berkah">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Alamat Toko (Dicetak di Header Struk)</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Contoh: Jl. Sudirman No. 45, Jakarta">{{ old('address', $setting->address) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nomor Telepon / WhatsApp Toko</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $setting->phone) }}" placeholder="Contoh: 08123456789">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Pesan Catatan Kaki Struk (Footer Message)</label>
                    <textarea name="footer_message" class="form-control" rows="2" placeholder="Contoh: Terima Kasih atas Kunjungan Anda!">{{ old('footer_message', $setting->footer_message) }}</textarea>
                </div>

                <hr class="my-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-qr-code me-2" style="color:var(--primary);"></i> Pembayaran QRIS</h5>
                <div class="form-check form-switch mb-3">
                    <input type="hidden" name="qris_enabled" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="qris_enabled" name="qris_enabled" value="1" @checked(old('qris_enabled', $setting->qris_enabled))>
                    <label class="form-check-label fw-bold" for="qris_enabled">Aktifkan pembayaran QRIS</label>
                    <div class="form-text">Jika dimatikan, pilihan QRIS tidak tampil di halaman kasir.</div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold" for="qris_image">Gambar QRIS</label>
                    <input type="file" name="qris_image" id="qris_image" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/*">
                    <div class="form-text">Format JPG, PNG, atau WEBP, maksimal 2 MB.</div>
                    @if($setting->qris_image)
                        <div class="mt-3 d-flex align-items-center gap-3">
                            <img src="{{ asset($setting->qris_image) }}" alt="QRIS toko" style="width:140px;height:140px;object-fit:contain;border:1px solid #dee2e6;border-radius:10px;padding:6px;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="qris_image_remove" value="1" id="qris_image_remove">
                                <label class="form-check-label" for="qris_image_remove">Hapus gambar QRIS</label>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-lg" style="border-radius:12px;">Batal</a>
                    <button type="submit" class="btn-save btn-lg">
                        <i class="bi bi-check-circle me-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
