<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk - Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{font-family:'Inter',sans-serif;}
        :root{--primary:#059669;--grad:linear-gradient(135deg,#059669 0%,#0D9488 100%);--bg:#f0fdf4;--shadow:0 4px 20px rgba(5,150,105,0.10);}
        body{background:var(--bg);color:#1e293b;}
        .app-nav{background:var(--grad);padding:0.9rem 1.75rem;box-shadow:0 4px 24px rgba(5,150,105,0.28);}
        .app-nav .brand{color:white;font-weight:800;font-size:1.15rem;display:flex;align-items:center;gap:0.6rem;}
        .btn-nav-back{background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:white;border-radius:10px;padding:0.4rem 1rem;font-weight:600;font-size:0.88rem;text-decoration:none;transition:all 0.2s;display:inline-flex;align-items:center;gap:0.4rem;}
        .btn-nav-back:hover{background:rgba(255,255,255,0.25);color:white;}
        .page-card{background:white;border-radius:20px;box-shadow:var(--shadow);border:none;padding:2rem;}
        .search-input{border:1.5px solid #e5e7eb;border-radius:10px;padding:0.6rem 1rem 0.6rem 2.5rem;font-size:0.9rem;transition:all 0.2s;width:240px;}
        .search-input:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(5,150,105,0.12);outline:none;}
        .btn-add{background:var(--grad);border:none;color:white;border-radius:12px;padding:0.62rem 1.25rem;font-weight:700;font-size:0.9rem;text-decoration:none;display:inline-flex;align-items:center;gap:0.4rem;transition:all 0.3s;}
        .btn-add:hover{opacity:0.9;transform:translateY(-1px);box-shadow:0 6px 20px rgba(5,150,105,0.3);color:white;}
        .table-premium thead tr th{background:var(--grad);color:white;font-weight:600;font-size:0.8rem;letter-spacing:0.4px;padding:0.9rem 1rem;border:none;}
        .table-premium thead tr th:first-child{border-radius:12px 0 0 0;}
        .table-premium thead tr th:last-child{border-radius:0 12px 0 0;}
        .table-premium tbody tr{border-bottom:1px solid #f0fdf4;transition:background 0.15s;}
        .table-premium tbody tr:hover{background:#f0fdf4;}
        .table-premium tbody td{padding:0.85rem 1rem;vertical-align:middle;border:none;}
        .table-premium tbody tr.low-stock{background:#fff5f5;}
        .table-premium tbody tr.low-stock:hover{background:#fee2e2;}
        .badge-kode{background:#f0fdf4;color:#065f46;border-radius:8px;padding:4px 10px;font-weight:600;font-size:0.78rem;}
        .blink{animation:blink 1.4s infinite;}
        @keyframes blink{0%,100%{opacity:1}50%{opacity:0.5}}
        .btn-edit{background:#fef3c7;color:#92400e;border:none;border-radius:9px;padding:5px 12px;font-weight:600;font-size:0.8rem;transition:all 0.2s;}
        .btn-edit:hover{background:#fbbf24;color:white;}
        .btn-del{background:#fee2e2;color:#991b1b;border:none;border-radius:9px;padding:5px 12px;font-weight:600;font-size:0.8rem;transition:all 0.2s;}
        .btn-del:hover{background:#ef4444;color:white;}
        .stock-alert{background:linear-gradient(135deg,#fef2f2,#fff5f5);border:1.5px solid #fecaca;border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.5rem;}
    </style>
</head>
<body>
    <nav class="app-nav d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn-nav-back"><i class="bi bi-arrow-left"></i> Dashboard</a>
            <div class="brand"><i class="bi bi-box-seam"></i> Daftar Produk</div>
        </div>
        <span style="color:rgba(255,255,255,0.8);font-size:0.85rem;">Kasir</span>
    </nav>

    <div class="container-fluid px-4 py-4">
        @if(session('success'))
            <div class="alert border-0 rounded-3 py-2 mb-3" style="background:#f0fdf4;color:#065f46;font-size:0.9rem;">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            </div>
        @endif

        @php $lowStock = $products->where('stok', '<=', 5); @endphp
        @if($lowStock->count() > 0)
        <div class="stock-alert">
            <div style="font-weight:700;color:#991b1b;margin-bottom:0.4rem;font-size:0.9rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $lowStock->count() }} produk stok menipis!</div>
            <div class="d-flex flex-wrap gap-1">
                @foreach($lowStock as $lp)
                    <span class="blink" style="background:#fecaca;color:#991b1b;border-radius:8px;padding:3px 10px;font-size:0.78rem;font-weight:600;">{{ $lp->nama_barang }} ({{ $lp->stok }})</span>
                @endforeach
            </div>
        </div>
        @endif

        <div class="page-card">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h5 style="font-weight:800;margin-bottom:0.1rem;">Daftar Produk</h5>
                    <div style="font-size:0.82rem;color:#94a3b8;">{{ $products->count() }} produk terdaftar @if($lowStock->count() > 0) · <span style="color:#ef4444;">{{ $lowStock->count() }} stok menipis</span>@endif</div>
                </div>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <div style="position:relative;">
                        <i class="bi bi-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:0.85rem;"></i>
                        <input type="text" id="searchBarang" class="search-input" placeholder="Cari nama/kode...">
                    </div>
                    <a href="{{ route('products.create') }}" class="btn-add"><i class="bi bi-plus-lg"></i> Tambah Produk</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-premium" style="border-collapse:separate;border-spacing:0;">
                    <thead>
                        <tr>
                            <th>Kode</th><th>Nama Barang</th>
                            <th class="text-end">Harga Beli</th><th class="text-end">Harga Jual</th>
                            <th class="text-center">Margin</th><th class="text-center">Stok</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bodyBarang">
                        @forelse($products as $p)
                        @php $margin = $p->harga_jual > 0 ? round((($p->harga_jual-$p->harga_beli)/$p->harga_jual)*100) : 0; @endphp
                        <tr class="{{ $p->stok <= 5 ? 'low-stock' : '' }}" data-search="{{ strtolower($p->nama_barang.' '.$p->kode_barang) }}">
                            <td><span class="badge-kode">{{ $p->kode_barang }}</span></td>
                            <td style="font-weight:600;">{{ $p->nama_barang }}</td>
                            <td class="text-end" style="color:#64748b;">Rp {{ number_format($p->harga_beli,0,',','.') }}</td>
                            <td class="text-end" style="font-weight:700;color:#059669;">Rp {{ number_format($p->harga_jual,0,',','.') }}</td>
                            <td class="text-center">
                                <span style="background:{{ $margin>=20?'#dcfce7':($margin>=10?'#fef3c7':'#fee2e2') }};color:{{ $margin>=20?'#166534':($margin>=10?'#92400e':'#991b1b') }};border-radius:8px;padding:3px 10px;font-weight:700;font-size:0.8rem;">{{ $margin }}%</span>
                            </td>
                            <td class="text-center">
                                @if($p->stok == 0)
                                    <span class="blink" style="background:#1e293b;color:white;border-radius:8px;padding:3px 10px;font-weight:700;font-size:0.78rem;">HABIS</span>
                                @elseif($p->stok <= 5)
                                    <span class="blink" style="background:#fee2e2;color:#991b1b;border-radius:8px;padding:3px 10px;font-weight:700;font-size:0.78rem;"><i class="bi bi-exclamation-triangle-fill"></i> {{ $p->stok }}</span>
                                @else
                                    <span style="background:#dcfce7;color:#166534;border-radius:8px;padding:3px 10px;font-weight:700;font-size:0.78rem;">{{ $p->stok }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('products.edit', $p->id) }}" class="btn-edit me-1"><i class="bi bi-pencil-fill"></i></a>
                                <form action="{{ route('products.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus barang \'{{ $p->nama_barang }}\'?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-del"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-5" style="color:#94a3b8;"><i class="bi bi-box-seam fs-1 d-block mb-2"></i>Belum ada barang. Silakan tambah.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div id="noBarangMsg" class="text-center py-4" style="color:#94a3b8;display:none;"><i class="bi bi-search fs-2 d-block mb-2"></i>Produk tidak ditemukan.</div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchBarang').addEventListener('input', function() {
            const kw = this.value.toLowerCase();
            let found = false;
            document.querySelectorAll('#bodyBarang tr[data-search]').forEach(r => {
                const m = r.getAttribute('data-search').includes(kw);
                r.style.display = m ? '' : 'none';
                if(m) found = true;
            });
            document.getElementById('noBarangMsg').style.display = found ? 'none' : 'block';
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>