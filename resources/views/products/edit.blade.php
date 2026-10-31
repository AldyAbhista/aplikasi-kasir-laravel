<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang - Kasir</title>
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
        .form-control{border:1.5px solid #e5e7eb;border-radius:10px;padding:0.7rem 1rem;transition:all 0.2s;}
        .form-control:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(5,150,105,0.12);}
        .btn-submit{background:var(--grad);border:none;color:white;border-radius:12px;padding:0.82rem;font-weight:700;width:100%;transition:all 0.3s;}
        .btn-submit:hover{opacity:0.92;transform:translateY(-1px);box-shadow:0 8px 25px rgba(5,150,105,0.35);color:white;}
        .btn-cancel{background:#f1f5f9;border:none;color:#475569;border-radius:12px;padding:0.82rem;font-weight:600;width:100%;text-decoration:none;display:block;text-align:center;transition:all 0.2s;}
        .btn-cancel:hover{background:#e2e8f0;color:#334155;}
        .preview-margin{background:#f0fdf4;border-radius:12px;padding:1rem;border:1.5px solid #a7f3d0;margin-top:0.5rem;}
    </style>
</head>
<body>
    <nav class="app-nav d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('products.index') }}" class="btn-nav-back"><i class="bi bi-arrow-left"></i> Daftar Produk</a>
            <div class="brand"><i class="bi bi-pencil-square"></i> Edit Produk</div>
        </div>
        <span style="color:rgba(255,255,255,0.8);font-size:0.85rem;">Kasir</span>
    </nav>

    <div class="container py-4" style="max-width:580px;">
        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3 py-2 mb-3" style="font-size:0.88rem;">
                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul>
            </div>
        @endif

        <div class="form-card">
            <h5 style="font-weight:800;margin-bottom:1.5rem;">Edit: {{ $product->nama_barang }}</h5>
            <form action="{{ route('products.update', $product->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Kode Barang</label>
                    <input type="text" name="kode_barang" class="form-control" value="{{ $product->kode_barang }}" required style="font-weight:700;letter-spacing:1px;">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" value="{{ $product->nama_barang }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga Beli (Modal)</label>
                    <div class="input-group">
                        <span style="background:#f8fafc;border:1.5px solid #e5e7eb;border-right:none;padding:0.7rem 1rem;border-radius:10px 0 0 10px;color:#64748b;font-weight:600;">Rp</span>
                        <input type="number" name="harga_beli" id="hargaBeli" class="form-control" value="{{ $product->harga_beli }}" required oninput="hitungMargin()" style="border-radius:0 10px 10px 0;">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga Jual</label>
                    <div class="input-group">
                        <span style="background:#f8fafc;border:1.5px solid #e5e7eb;border-right:none;padding:0.7rem 1rem;border-radius:10px 0 0 10px;color:#64748b;font-weight:600;">Rp</span>
                        <input type="number" name="harga_jual" id="hargaJual" class="form-control" value="{{ $product->harga_jual }}" required oninput="hitungMargin()" style="border-radius:0 10px 10px 0;">
                    </div>
                    <div class="preview-margin" id="previewMargin">
                        <div style="font-size:0.83rem;color:#065f46;">
                            <i class="bi bi-graph-up-arrow me-1"></i>
                            Margin: <strong id="marginText">0%</strong> &nbsp;|&nbsp; Profit per item: <strong id="profitText">Rp 0</strong>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" value="{{ $product->stok }}" required>
                    @if($product->stok <= 5)
                        <div style="color:#ef4444;font-size:0.82rem;margin-top:0.4rem;font-weight:600;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Stok menipis! Segera perbarui.</div>
                    @endif
                </div>
                <button type="submit" class="btn-submit mb-2"><i class="bi bi-check-circle me-1"></i> Simpan Perubahan</button>
                <a href="{{ route('products.index') }}" class="btn-cancel"><i class="bi bi-arrow-left me-1"></i> Batal</a>
            </form>
        </div>
    </div>
    <script>
        function hitungMargin() {
            const beli = parseInt(document.getElementById('hargaBeli').value)||0;
            const jual = parseInt(document.getElementById('hargaJual').value)||0;
            if (beli > 0 && jual > 0) {
                const profit = jual - beli;
                const margin = Math.round((profit/jual)*100);
                document.getElementById('marginText').textContent = margin + '%';
                document.getElementById('profitText').textContent = 'Rp ' + profit.toLocaleString('id-ID');
            }
        }
        hitungMargin();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>