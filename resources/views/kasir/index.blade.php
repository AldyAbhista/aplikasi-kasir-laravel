<!DOCTYPE html>
<html lang="id">
<head>

@if ($errors->any())
    <div style="background: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #f5c6cb;">
        <strong>Aduh, Ada Validasi yang Gagal:</strong>
        <ul style="margin-top: 5px; margin-bottom: 0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div style="background: #fff3cd; color: #856404; padding: 12px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #ffeeba;">
        <strong>Pesan Error:</strong> {{ session('error') }}
    </div>
@endif

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kasir Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0fdf4; font-family: 'Inter', sans-serif; }
        .product-card { cursor: pointer; border-radius: 12px; border: 2px solid transparent; transition: 0.2s; }
        .product-card:hover { border-color: #059669; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(5,150,105,0.18); }
        .cashier-main { align-items: flex-start; }
        .cashier-main > [class*="col-"] { min-height: 0; }
        .cart-container { height: auto; min-height: 0; overflow-y: visible; background: #fff; border-left: 1px solid #ddd; align-self: flex-start; }
        @media (max-width: 768px) {
            .cart-container {
                height: auto;
                min-height: 500px;
                border-left: none;
                border-top: 2px solid #e5e7eb;
                margin-top: 1.5rem;
            }
        }
        .btn-bulat { width: 30px; height: 30px; padding: 0; border-radius: 50%; font-weight: bold; }
        
        /* Success Modal Styling */
        .success-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .success-modal-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            text-align: center;
            width: min(390px, calc(100vw - 32px));
            max-height: calc(100vh - 32px);
            overflow-y: auto;
            animation: popIn 0.5s ease-out;
        }
        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: white;
        }
        .success-modal-card h2 {
            color: #28a745;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 1.65rem;
        }
        .success-modal-card p {
            color: #666;
            font-size: 14px;
            margin-bottom: 14px;
        }
        .qris-modal-image { width: 170px; height: 170px; }
        .qris-modal-button { width: 100%; font-size: 1rem; padding: .65rem 1rem; }
        @media (max-width: 768px) {
            .cart-container { height: auto; min-height: 500px; }
            .success-modal-card { padding: 18px; border-radius: 16px; }
            .qris-modal-image { width: 150px; height: 150px; }
        }
        .checkout-panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 18px; padding: 1rem; }
        .summary-row { display:flex; justify-content:space-between; gap:1rem; padding:.35rem 0; color:#64748b; }
        .summary-row strong { color:#1e293b; }
        .summary-total { border-top:1px dashed #cbd5e1; margin-top:.5rem; padding-top:.75rem; }
    </style>
</head>
<body>
    <nav style="background:linear-gradient(135deg,#059669 0%,#0D9488 100%);box-shadow:0 4px 24px rgba(5,150,105,0.28);padding:0.7rem 1.5rem;" class="d-flex align-items-center justify-content-between">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center gap-3">
                @if(auth()->user()?->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" style="background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:white;border-radius:10px;padding:0.4rem 0.9rem;font-weight:600;font-size:0.85rem;text-decoration:none;" class="d-inline-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left"></i> Dashboard
                    </a>
                @endif
                @php
                    $storeName = \App\Models\StoreSetting::getSetting()->store_name ?? 'Kasir';
                @endphp
                <span class="navbar-brand mb-0 h5 fw-bold" style="color:white;">
                    <i class="bi bi-shop"></i> {{ $storeName }}
                    <span class="badge ms-2 fs-6" style="background:rgba(255,255,255,0.2);color:white;">{{ auth()->user()?->name }}</span>
                </span>
            </div>
            @if(auth()->user()?->role === 'kasir')
            <div class="d-flex gap-2">
                <a href="{{ route('kasir.history') }}" style="background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:white;border-radius:10px;padding:0.4rem 0.9rem;font-weight:600;font-size:0.85rem;text-decoration:none;" class="d-inline-flex align-items-center gap-1">
                    <i class="bi bi-clock-history"></i> Riwayat
                </a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" style="background:rgba(239,68,68,0.25);border:1.5px solid rgba(255,150,150,0.4);color:white;border-radius:10px;padding:0.4rem 0.9rem;font-weight:600;font-size:0.85rem;">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </div>
            @endif
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row cashier-main">
            <!-- Success Modal -->
            @if(session('success'))
            <div class="success-modal-overlay" id="successModal" role="dialog" aria-modal="true" aria-labelledby="successModalTitle" tabindex="-1">
                <div class="success-modal-card">
                    @if(session('qris_pending'))
                        <div class="mb-3">
                            <div class="fw-bold text-info mb-2"><i class="bi bi-qr-code me-1"></i> Scan QRIS untuk membayar</div>
                            <div class="rounded-3 px-3 py-2 mb-3" style="background:#ecfdf5;border:1px solid #a7f3d0;">
                                <div class="small text-secondary">Total yang harus dibayar</div>
                                <div class="fw-bold text-success fs-3">Rp {{ number_format((int) session('transaction_total', 0), 0, ',', '.') }}</div>
                            </div>
                            @if($setting->qris_image)
                                <img src="{{ asset($setting->qris_image) }}" alt="QRIS pembayaran" class="qris-modal-image" style="object-fit:contain;border:1px solid #dee2e6;border-radius:12px;padding:8px;">
                            @endif
                        </div>
                    @else
                    <div class="success-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    @endif
                    <h2 id="successModalTitle">{{ session('qris_pending') ? 'Menunggu Pembayaran QRIS' : (session('qris_confirmed_waiting') ? 'Pembayaran QRIS Dikonfirmasi' : 'Pembayaran Berhasil! ✓') }}</h2>
                <p id="transactionMessage">{{ session('qris_pending') ? 'Pesanan belum disimpan. Konfirmasi pembayaran setelah pelanggan menyelesaikan pembayaran QRIS.' : (session('qris_confirmed_waiting') ? 'Klik lanjutkan untuk membuka ringkasan pembayaran dan mencetak struk.' : 'Transaksi telah berhasil diproses') }}</p>
                    
                    <div class="d-grid gap-2">
                        @if(session('qris_pending'))
                        <form action="{{ route('kasir.qris.confirm') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning fw-bold rounded-pill qris-modal-button">
                                <i class="bi bi-check-circle me-1"></i> Konfirmasi Pembayaran QRIS
                            </button>
                        </form>
                        <button type="button" class="btn btn-outline-secondary fw-bold rounded-pill qris-modal-button" disabled aria-disabled="true" style="opacity:.55;">
                            <i class="bi bi-lock me-1"></i> Lanjut Transaksi (Konfirmasi dulu)
                        </button>
                        <form action="{{ route('kasir.qris.cancel') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger fw-bold rounded-pill qris-modal-button">
                                <i class="bi bi-x-circle me-1"></i> Batalkan QRIS & Ganti Pembayaran
                            </button>
                        </form>
                        @elseif(session('qris_confirmed_waiting') && session('transaction_id'))
                        <button type="button" class="btn btn-success fw-bold rounded-pill qris-modal-button" disabled aria-disabled="true">
                            <i class="bi bi-check-circle-fill me-1"></i> Pembayaran QRIS Dikonfirmasi
                        </button>
                        <a href="{{ route('kasir.struk', session('transaction_id')) }}" target="_blank" onclick="mulaiTransaksiBaru()" class="btn btn-primary fw-bold rounded-pill qris-modal-button">
                            <i class="bi bi-arrow-right-circle me-1"></i> Lanjut Transaksi & Cetak Struk
                        </a>
                        <button type="button" class="btn btn-outline-secondary fw-bold rounded-pill qris-modal-button" disabled aria-disabled="true" style="opacity:.55;">
                            <i class="bi bi-lock me-1"></i> Pembayaran Sudah Disimpan
                        </button>
                        @elseif(session('transaction_id'))
                        <a href="{{ route('kasir.struk', session('transaction_id')) }}" target="_blank" onclick="mulaiTransaksiBaru()" class="btn btn-success btn-lg fw-bold rounded-pill">
                            <i class="bi bi-printer"></i> Cetak Struk & Tutup
                        </a>
                        <button type="button" class="btn btn-outline-secondary btn-lg fw-bold rounded-pill" onclick="closeSuccess()">
                            <i class="bi bi-x"></i> Lanjut Transaksi
                        </button>
                        @else
                        <button type="button" class="btn btn-success btn-lg fw-bold rounded-pill" onclick="closeSuccess()">
                            <i class="bi bi-check"></i> Tutup
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="col-md-8 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="fw-bold text-secondary mb-0">Pilih Menu</h4>
                    <div class="input-group" style="max-width: 260px;">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <label for="searchProduk" class="visually-hidden">Cari produk</label>
                        <input type="search" id="searchProduk" class="form-control border-start-0 ps-0" placeholder="Cari produk..." oninput="filterProduk()">
                    </div>
                </div>

                <div class="row" id="gridProduk">
                    @foreach($products as $p)
                    <div class="col-md-3 col-sm-4 mb-3 produk-item" data-nama="{{ strtolower($p->nama_barang) }}">
                        <div class="card product-card h-100 shadow-sm"
                             role="button"
                             tabindex="0"
                             aria-label="Pilih produk {{ $p->nama_barang }}, harga Rp {{ number_format($p->harga_jual, 0, ',', '.') }}, sisa stok {{ $p->stok }}"
                             data-id="{{ $p->id }}"
                             data-nama="{{ e($p->nama_barang) }}"
                             data-harga="{{ $p->harga_jual }}"
                             data-stok="{{ $p->stok }}"
                             onclick="klikMenuFromEl(this)"
                             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault(); klikMenuFromEl(this);}">
                            <div class="card-body text-center p-3">
                                <h6 class="fw-bold text-dark mb-2">{{ $p->nama_barang }}</h6>
                                <h5 class="fw-bold mb-2" style="color:#059669;">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</h5>
                                @if($p->stok <= 5)
                                    <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill"></i> Stok: {{ $p->stok }}</span>
                                @else
                                    <span class="badge bg-secondary">Stok: {{ $p->stok }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div id="noResultMsg" class="col-12 text-center text-muted py-4" style="display:none;">
                        <i class="bi bi-search fs-1"></i><br>Produk tidak ditemukan.
                    </div>
                </div>
            </div>

            <div class="col-md-4 p-4 cart-container d-flex flex-column">
                <h4 class="fw-bold mb-3 text-secondary"><i class="bi bi-receipt-cutoff"></i> Kasir</h4>
                
                <form action="{{ route('kasir.store') }}" method="POST" id="form-kasir" class="flex-grow-1 d-flex flex-column">
                    @csrf

                    <ul class="nav nav-tabs nav-fill mb-3" id="kasirTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="pesanan-tab" data-bs-toggle="tab" data-bs-target="#pesanan-pane" type="button" role="tab" aria-controls="pesanan-pane" aria-selected="true">
                                <i class="bi bi-cart3 me-1"></i> Kasir
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="pembayaran-tab" data-bs-toggle="tab" data-bs-target="#pembayaran-pane" type="button" role="tab" aria-controls="pembayaran-pane" aria-selected="false">
                                <i class="bi bi-calculator me-1"></i> Ringkasan Pembayaran
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content flex-grow-1" id="kasirTabsContent">
                        <div class="tab-pane fade show active h-100" id="pesanan-pane" role="tabpanel" aria-labelledby="pesanan-tab" tabindex="0">
                            <div class="checkout-panel mb-3">
                                <div class="mb-3">
                                    <label for="customer_name" class="form-label fw-bold text-secondary"><i class="bi bi-person"></i> Nama Pembeli</label>
                                    <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="Opsional" autocomplete="off">
                                </div>
                                <div class="mb-3">
                                    <label for="order_notes" class="form-label fw-bold text-secondary"><i class="bi bi-chat-left-text"></i> Catatan Pesanan</label>
                                    <textarea id="order_notes" name="notes" class="form-control" rows="2" placeholder="Contoh: tidak pedas, antar ke meja 4" autocomplete="off"></textarea>
                                </div>
                                <label class="form-label fw-bold text-secondary"><i class="bi bi-shop-window"></i> Tipe Pesanan</label>
                                <div class="btn-group w-100" role="group" aria-label="Tipe pesanan">
                                    <input type="radio" class="btn-check" name="order_type" id="order_dine_in" value="dine_in" checked>
                                    <label class="btn btn-outline-success fw-bold" for="order_dine_in"><i class="bi bi-cup-hot me-1"></i> Dine In</label>
                                    <input type="radio" class="btn-check" name="order_type" id="order_takeaway" value="takeaway">
                                    <label class="btn btn-outline-warning fw-bold" for="order_takeaway"><i class="bi bi-bag-check me-1"></i> Takeaway</label>
                                </div>
                            </div>

                            <div id="keranjang-list" class="mb-3"></div>

                            <button type="button" id="btn-lanjut-pembayaran" class="btn btn-success w-100 fw-bold" disabled>
                                Lanjut ke Ringkasan Pembayaran <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>

                        <div class="tab-pane fade" id="pembayaran-pane" role="tabpanel" aria-labelledby="pembayaran-tab" tabindex="0">
                            <div class="checkout-panel">
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-calculator me-1"></i> Ringkasan Pembayaran</h6>
                                <div class="border rounded-3 p-3 mb-3 bg-light-subtle">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="text-dark"><i class="bi bi-basket me-1"></i> Ringkasan Pesanan</strong>
                                        <span class="badge text-bg-success" id="ringkasan-tipe-pesanan">DINE IN</span>
                                    </div>
                                    <div class="border-top pt-2 mb-2">
                                        <span class="text-secondary"><i class="bi bi-person me-1"></i>Nama Pembeli:</span>
                                        <strong class="text-dark" id="ringkasan-nama-pembeli">-</strong>
                                    </div>
                                    <div id="ringkasan-pesanan-list"></div>
                                    <div class="border-top pt-2 mt-2">
                                        <span class="text-secondary"><i class="bi bi-chat-left-text me-1"></i>Catatan Pesanan:</span>
                                        <p class="small text-dark mb-0 mt-1" id="ringkasan-catatan-pesanan">-</p>
                                    </div>
                                </div>
                                <div class="summary-row"><span>Subtotal</span><strong id="teks-subtotal">Rp 0</strong></div>
                                <div class="summary-row"><span>Pajak</span><strong id="teks-pajak">Rp 0</strong></div>
                                <div class="summary-row align-items-center">
                                    <label for="discount_type" class="mb-0">Diskon</label>
                                    <div class="input-group input-group-sm" style="max-width:185px;">
                                        <select name="discount_type" id="discount_type" class="form-select">
                                            <option value="">Tidak ada</option>
                                            <option value="percent">Persen (%)</option>
                                            <option value="fixed">Nominal (Rp)</option>
                                        </select>
                                        <input type="number" name="discount_value" id="discount_value" class="form-control" min="0" step="0.01" value="0" disabled aria-label="Nilai diskon">
                                    </div>
                                </div>
                                <div class="summary-row"><span>Nilai diskon</span><strong id="teks-diskon">Rp 0</strong></div>
                                <div class="summary-row summary-total"><span class="fw-bold text-dark">Total Bayar</span><strong class="fs-5 text-success" id="teks-total">Rp 0</strong></div>

                                <input type="hidden" name="total_harga" id="input-total" value="0">
                                <input type="hidden" name="kembalian" id="input-kembalian" value="0">

                                <div class="mb-3 mt-3">
                                    <label class="form-label fw-bold text-secondary">Metode Pembayaran</label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="payment_method" id="payment_cash" value="cash" checked>
                                        <label class="btn btn-outline-success w-50 fw-bold" for="payment_cash"><i class="bi bi-cash-coin"></i> Cash</label>
                                        @if($setting->qris_enabled)
                                        <input type="radio" class="btn-check" name="payment_method" id="payment_qris" value="qris">
                                        <label class="btn btn-outline-info w-50 fw-bold" for="payment_qris"><i class="bi bi-phone"></i> QRIS</label>
                                        @endif
                                    </div>
                                </div>

                                @if($setting->qris_enabled)
                                <div class="mb-3" id="bank-section" style="display: none;">
                                    <label class="form-label fw-bold text-secondary">Bank/E-Wallet</label>
                                    <select name="bank" id="select-bank" class="form-select form-select-lg">
                                        <option value="">-- Pilih Bank --</option>
                                        <option value="BCA">BCA</option>
                                        <option value="Mandiri">Mandiri</option>
                                        <option value="BRI">BRI</option>
                                        <option value="Permata">Permata</option>
                                        <option value="OVO">OVO</option>
                                        <option value="DANA">DANA</option>
                                        <option value="Gopay">Gopay</option>
                                        <option value="LinkAja">LinkAja</option>
                                        <option value="Lainnya">Lainnya...</option>
                                    </select>
                                    <input type="text" id="input-bank-lain" name="bank_custom" class="form-control mt-2" placeholder="Ketik nama bank/e-wallet" style="display: none;">
                                </div>
                                @endif

                                <div class="mb-3 mt-3">
                                    <label for="input-bayar-display" class="form-label fw-bold text-secondary">Uang Bayar (Rp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold" style="background:#f0fdf4;border-color:#a7f3d0;color:#059669;">Rp</span>
                                        <input type="text" id="input-bayar-display" class="form-control form-control-lg fw-bold" placeholder="0" inputmode="numeric" autocomplete="off" style="border-color:#a7f3d0;letter-spacing:0.5px;" oninput="formatBayarInput(this)">
                                    </div>
                                    <input type="hidden" name="bayar" id="input-bayar" value="0">
                                </div>

                                <div class="d-flex justify-content-between mb-4">
                                    <span class="fw-bold text-secondary">Kembalian:</span>
                                    <h5 class="fw-bold text-success mb-0" id="teks-kembalian">Rp 0</h5>
                                </div>

                                <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold rounded-pill shadow" id="btn-bayar" disabled>
                                    <i class="bi bi-printer"></i> PROSES PEMBAYARAN
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Item ke Keranjang -->
    <div class="modal fade" id="modalTambahItem" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah (Qty)</label>
                        <input type="number" id="inputQty" class="form-control form-control-lg" value="1" min="1" aria-describedby="qtyStockWarning">
                        <div id="qtyStockWarning" class="text-danger fw-semibold small mt-2" role="alert" style="display:none;"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="bi bi-chat-left-text"></i> Catatan untuk Item Ini (Opsional)</label>
                        <textarea id="inputItemNotes" class="form-control" rows="2" ></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnTambahItem" class="btn btn-success fw-bold" onclick="confirmTambahItem()">Tambah ke Keranjang</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Catatan Item -->
    <div class="modal fade" id="modalEditNotes" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold" id="modalEditTitle">Edit Catatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="bi bi-chat-left-text"></i> Catatan Item</label>
                        <textarea id="inputEditNotes" class="form-control" rows="3" placeholder="Masukkan catatan item"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning fw-bold text-dark" onclick="saveEditNotes()">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi Pencarian Produk
        function filterProduk() {
            const keyword = document.getElementById('searchProduk').value.toLowerCase();
            const items = document.querySelectorAll('.produk-item');
            let ada = false;
            items.forEach(item => {
                const nama = item.getAttribute('data-nama');
                if (nama.includes(keyword)) {
                    item.style.display = '';
                    ada = true;
                } else {
                    item.style.display = 'none';
                }
            });
            document.getElementById('noResultMsg').style.display = ada ? 'none' : 'block';
        }

        let keranjang = [];
        let modalTambahItem;
        let modalEditNotes;
        let itemTerpilih = { id: null, nama: null, harga: null, stok: null };
        let indexEditNotes = null;
        let isNavigatingForSubmit = false;
        const transactionSucceeded = @json(session()->has('success'));
        const restoredQrisPayment = @json(session('restore_qris_payment'));

        // Initialize Bootstrap Modal
        document.addEventListener('DOMContentLoaded', function() {
            modalTambahItem = new bootstrap.Modal(document.getElementById('modalTambahItem'));
            modalEditNotes = new bootstrap.Modal(document.getElementById('modalEditNotes'));
            if (transactionSucceeded) {
                hapusDraft();
                resetTransaksi();
            } else {
                if (restoredQrisPayment) {
                    draftStorage.setItem(draftKey, JSON.stringify(restoredQrisPayment));
                }
                pulihkanDraft();
            }
            renderLayar();
        });

        // 1. Fungsi Format Rupiah
        function formatUang(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, character => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            })[character]);
        }

        // 2. Fungsi Klik Menu (Buka Modal)
        function klikMenu(id, nama, harga, stok) {
            itemTerpilih = { id: id, nama: nama, harga: harga, stok: stok };
            document.getElementById('modalTitle').innerText = `${nama}`;
            const qtyInput = document.getElementById('inputQty');
            const remainingStock = stok - totalQtyForProduct(id);
            qtyInput.value = 1;
            qtyInput.min = 1;
            qtyInput.removeAttribute('max');
            qtyInput.dataset.remainingStock = Math.max(remainingStock, 0);
            document.getElementById('inputItemNotes').value = '';
            updateQtyStockWarning();
            modalTambahItem.show();
        }

        function klikMenuFromEl(el) {
            const id = parseInt(el.getAttribute('data-id'));
            const nama = el.getAttribute('data-nama');
            const harga = parseFloat(el.getAttribute('data-harga'));
            const stok = parseInt(el.getAttribute('data-stok'));
            klikMenu(id, nama, harga, stok);
        }

        // 2b. Fungsi Confirm Tambah Item (dari Modal)
        function confirmTambahItem() {
            let qty = parseInt(document.getElementById('inputQty').value) || 1;
            let notes = document.getElementById('inputItemNotes').value.trim();
            let { id, nama, harga, stok } = itemTerpilih;
            let currentQty = totalQtyForProduct(id);

            if (qty < 1) {
                updateQtyStockWarning('Jumlah minimal 1.');
                return;
            }

            if (currentQty + qty > stok) {
                updateQtyStockWarning();
                return;
            }

            // Cari apakah barang sudah ada di keranjang dengan catatan sama
            let item = keranjang.find(i => i.id === id && i.notes === notes);

            if (item) {
                item.qty += qty;
            } else {
                // Masukkan barang baru
                keranjang.push({ 
                    id: id, 
                    nama: nama, 
                    harga: harga, 
                    qty: qty, 
                    stok: stok,
                    notes: notes
                });
            }
            
            modalTambahItem.hide();
            renderLayar();
        }

        function updateQtyStockWarning(customMessage = null) {
            const qtyInput = document.getElementById('inputQty');
            const warning = document.getElementById('qtyStockWarning');
            const addButton = document.getElementById('btnTambahItem');
            const quantity = Number.parseInt(qtyInput.value, 10);
            const remainingStock = Number(qtyInput.dataset.remainingStock || 0);
            const hasInvalidQuantity = !Number.isInteger(quantity) || quantity < 1;
            const exceedsStock = Number.isInteger(quantity) && quantity > remainingStock;

            if (customMessage || hasInvalidQuantity || exceedsStock) {
                warning.textContent = customMessage || `Stok tidak mencukupi. Sisa stok yang dapat dipesan: ${remainingStock}.`;
                warning.style.display = 'block';
                addButton.disabled = true;
                return false;
            }

            warning.textContent = '';
            warning.style.display = 'none';
            addButton.disabled = false;
            return true;
        }

        function totalQtyForProduct(productId) {
            return keranjang
                .filter(item => item.id === productId)
                .reduce((total, item) => total + item.qty, 0);
        }

        document.getElementById('inputQty').addEventListener('input', function() {
            updateQtyStockWarning();
        });

        // 3. Fungsi Edit Notes
        function editNotes(index) {
            indexEditNotes = index;
            let item = keranjang[index];
            document.getElementById('modalEditTitle').innerText = `Edit Catatan - ${item.nama}`;
            document.getElementById('inputEditNotes').value = item.notes || '';
            modalEditNotes.show();
        }

        // 3b. Fungsi Save Edit Notes
        function saveEditNotes() {
            if (indexEditNotes !== null) {
                let notesValue = document.getElementById('inputEditNotes').value.trim();
                keranjang[indexEditNotes].notes = notesValue;
                modalEditNotes.hide();
                renderLayar();
            }
        }

        // 4. Fungsi Tambah Qty (Tombol +)
        function tambahQty(index) {
            let item = keranjang[index];
            if (item && item.qty < item.stok) {
                item.qty++;
                renderLayar();
            } else {
                alert('Stok maksimal sudah tercapai!');
            }
        }

        // 5. Fungsi Kurang Qty (Tombol -)
        function kurangQty(index) {
            if (keranjang[index].qty > 1) {
                keranjang[index].qty--;
            } else {
                keranjang.splice(index, 1);
            }
            renderLayar();
        }

        // 6. Fungsi Hapus Barang (Tombol Merah)
        function hapusBarang(index) {
            keranjang.splice(index, 1);
            renderLayar();
        }

        function hitungRingkasan(subtotal) {
            const tipeDiskon = document.getElementById('discount_type').value;
            const nilaiDiskon = parseFloat(document.getElementById('discount_value').value) || 0;
            const diskon = tipeDiskon === 'percent'
                ? Math.min(subtotal, Math.round(subtotal * Math.min(nilaiDiskon, 100) / 100))
                : Math.min(subtotal, Math.round(nilaiDiskon));
            const dasarPajak = subtotal - diskon;
            const pajak = Math.round(dasarPajak * 11 / 100);
            const total = dasarPajak + pajak;

            document.getElementById('teks-subtotal').innerText = 'Rp ' + formatUang(subtotal);
            document.getElementById('teks-pajak').innerText = 'Rp ' + formatUang(pajak);
            document.getElementById('teks-diskon').innerText = 'Rp ' + formatUang(diskon);
            document.getElementById('teks-total').innerText = 'Rp ' + formatUang(total);
            document.getElementById('input-total').value = total;

            return total;
        }

        function renderRingkasanPesanan() {
            const summaryList = document.getElementById('ringkasan-pesanan-list');
            const lanjutButton = document.getElementById('btn-lanjut-pembayaran');
            const tipePesanan = document.querySelector('input[name="order_type"]:checked')?.value || 'dine_in';
            const tipeLabel = tipePesanan === 'takeaway' ? 'TAKEAWAY' : 'DINE IN';
            const namaPembeli = document.getElementById('customer_name').value.trim();
            const catatanPesanan = document.getElementById('order_notes').value.trim();

            document.getElementById('ringkasan-tipe-pesanan').innerText = tipeLabel;
            document.getElementById('ringkasan-nama-pembeli').innerText = namaPembeli || '-';
            document.getElementById('ringkasan-catatan-pesanan').innerText = catatanPesanan || '-';
            lanjutButton.disabled = keranjang.length === 0;

            if (keranjang.length === 0) {
                summaryList.innerHTML = '<p class="text-muted small mb-0">Belum ada menu yang ditambahkan.</p>';
                return;
            }

            summaryList.innerHTML = keranjang.map(item => `
                <div class="border-top py-2">
                    <div class="d-flex justify-content-between gap-2">
                        <span class="fw-semibold text-dark">${escapeHtml(item.nama)} <span class="text-muted">x${item.qty}</span></span>
                        <span class="fw-semibold text-dark">Rp ${formatUang(item.harga * item.qty)}</span>
                    </div>
                    <small class="text-muted">
                        ${item.notes ? `<i class="bi bi-chat-left-text me-1"></i>Catatan: ${escapeHtml(item.notes)}` : 'Tidak ada catatan item'}
                    </small>
                </div>
            `).join('');
        }

        function bukaTabPembayaran() {
            if (keranjang.length === 0) {
                return;
            }

            const tabButton = document.getElementById('pembayaran-tab');
            if (window.bootstrap && bootstrap.Tab) {
                bootstrap.Tab.getOrCreateInstance(tabButton).show();
                return;
            }

            document.querySelectorAll('#kasirTabs .nav-link').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('#kasirTabsContent .tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
            tabButton.classList.add('active');
            document.getElementById('pembayaran-pane').classList.add('show', 'active');
        }

        // 7. Fungsi Render Layar Keranjang
        function renderLayar() {
            let html = '';
            let subtotal = 0;

            if (keranjang.length === 0) {
                html = '<p class="text-center text-muted mt-5"><i class="bi bi-cart-x fs-1"></i><br>Keranjang Kosong</p>';
            } else {
                keranjang.forEach((item, index) => {
                    let itemSubtotal = item.harga * item.qty;
                    subtotal += itemSubtotal;

                    html += `
                    <div class="card mb-2 border-0 shadow-sm">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-bold text-dark">${escapeHtml(item.nama)}</h6>
                                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" aria-label="Hapus ${escapeHtml(item.nama)} dari keranjang" onclick="hapusBarang(${index})"><i class="bi bi-trash-fill"></i></button>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold" style="color:#059669;">Rp ${formatUang(itemSubtotal)}</span>
                                
                                <div class="d-flex align-items-center bg-light border rounded-pill p-1">
                                    <button type="button" class="btn btn-sm btn-light border btn-bulat text-danger" aria-label="Kurangi jumlah ${item.nama}" onclick="kurangQty(${index})">-</button>
                                    <span class="mx-3 fw-bold">${item.qty}</span>
                                    <button type="button" class="btn btn-sm btn-bulat text-white fw-bold" style="background:#059669;border:none;" aria-label="Tambah jumlah ${item.nama}" onclick="tambahQty(${index})">+</button>
                                </div>
                            </div>

                            ${item.notes ? `
                            <div class="alert alert-light p-2 mb-0 d-flex justify-content-between align-items-start border">
                                <small><strong><i class="bi bi-chat-left-text"></i> Catatan:</strong> ${escapeHtml(item.notes)}</small>
                                <button type="button" class="btn btn-sm btn-outline-warning py-0 px-2" aria-label="Edit catatan ${item.nama}" title="Edit catatan" onclick="editNotes(${index})"><i class="bi bi-pencil-square" aria-hidden="true"></i></button>
                            </div>
                            ` : `
                            <div class="alert alert-light p-2 mb-0 border">
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="editNotes(${index})"><i class="bi bi-plus-circle" aria-hidden="true"></i> Tambah Catatan</button>
                            </div>
                            `}

                            <input type="hidden" name="produk_id[]" value="${item.id}">
                            <input type="hidden" name="jumlah[]" value="${item.qty}">
                            <input type="hidden" name="item_notes[]" value="${item.notes || ''}">
                        </div>
                    </div>
                    `;
                });
            }

            // Update layar web
            document.getElementById('keranjang-list').innerHTML = html;
            renderRingkasanPesanan();
            const total = hitungRingkasan(subtotal);

            let paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';
            if (paymentMethod === 'qris' && total > 0) {
                document.getElementById('input-bayar').value = total;
                document.getElementById('input-bayar-display').value = formatUang(total);
            }
            
            kalkulasiUang();
            if (keranjang.length > 0 || document.getElementById('customer_name').value.trim() !== '' || document.getElementById('order_notes').value.trim() !== '') {
                simpanDraft();
            } else {
                hapusDraft();
            }
        }

        const draftKey = 'kasir-transaksi-draft';
        const draftStorage = window.sessionStorage;

        function simpanDraft() {
            draftStorage.setItem(draftKey, JSON.stringify({
                keranjang,
                customerName: document.getElementById('customer_name').value,
                notes: document.getElementById('order_notes').value,
                orderType: document.querySelector('input[name="order_type"]:checked')?.value || 'dine_in',
                discountType: document.getElementById('discount_type').value,
                discountValue: document.getElementById('discount_value').value
            }));
        }

        function hapusDraft() {
            draftStorage.removeItem(draftKey);
        }

        function resetTransaksi() {
            keranjang = [];
            document.getElementById('customer_name').value = '';
            document.getElementById('order_notes').value = '';
            document.getElementById('discount_type').value = '';
            document.getElementById('discount_value').value = 0;
            document.getElementById('discount_value').disabled = true;
            document.getElementById('payment_cash').checked = true;
            document.getElementById('input-bayar').value = 0;
            document.getElementById('input-bayar-display').value = '';
            const selectBank = document.getElementById('select-bank');
            const inputBankLain = document.getElementById('input-bank-lain');
            const bankSection = document.getElementById('bank-section');
            if (selectBank) selectBank.value = '';
            if (inputBankLain) {
                inputBankLain.value = '';
                inputBankLain.style.display = 'none';
            }
            if (bankSection) bankSection.style.display = 'none';
            document.getElementById('order_dine_in').checked = true;
        }

        function pulihkanDraft() {
            const draft = JSON.parse(draftStorage.getItem(draftKey) || 'null');
            if (!draft) return;
            keranjang = Array.isArray(draft.keranjang) ? draft.keranjang : [];
            document.getElementById('customer_name').value = draft.customerName || '';
            document.getElementById('order_notes').value = draft.notes || '';
            document.getElementById('discount_type').value = draft.discountType || '';
            document.getElementById('discount_value').value = draft.discountValue || 0;
            document.getElementById('discount_value').disabled = !draft.discountType;
            const orderType = document.querySelector(`input[name="order_type"][value="${draft.orderType || 'dine_in'}"]`);
            if (orderType) orderType.checked = true;
        }

        document.getElementById('btn-lanjut-pembayaran').addEventListener('click', bukaTabPembayaran);

        document.querySelectorAll('input[name="order_type"]').forEach(radio => {
            radio.addEventListener('change', renderRingkasanPesanan);
        });
        document.getElementById('customer_name').addEventListener('input', renderRingkasanPesanan);
        document.getElementById('order_notes').addEventListener('input', renderRingkasanPesanan);

        document.getElementById('discount_type').addEventListener('change', function () {
            const input = document.getElementById('discount_value');
            input.disabled = this.value === '';
            input.max = this.value === 'percent' ? 100 : '';
            if (this.value === '') {
                input.value = 0;
            }
            renderLayar();
        });

        document.getElementById('discount_value').addEventListener('input', renderLayar);

        // 8. Format Input Uang Bayar
        function formatBayarInput(input) {
            let value = input.value.replace(/\D/g, '');
            let numberValue = parseInt(value) || 0;
            document.getElementById('input-bayar').value = numberValue;
            input.value = numberValue > 0 ? formatUang(numberValue) : '';
            kalkulasiUang();
        }

        // 9. Kalkulasi Uang Kembalian & Enable/Disable Tombol Bayar
        function kalkulasiUang() {
            let total = parseInt(document.getElementById('input-total').value) || 0;
            let bayar = parseInt(document.getElementById('input-bayar').value) || 0;
            let bayarDisplay = document.getElementById('input-bayar-display').value.trim();
            let paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';
            let kembali = bayar - total;

            let teksKembalian = document.getElementById('teks-kembalian');
            let btnBayar = document.getElementById('btn-bayar');

            // Keranjang Kosong / Total 0
            if (keranjang.length === 0 || total === 0) {
                btnBayar.disabled = true;
                teksKembalian.innerText = 'Rp 0';
                teksKembalian.className = 'fw-bold mb-0 text-success';
                document.getElementById('input-kembalian').value = 0;
                return;
            }

            // Uang bayar belum diisi
            if (bayarDisplay === '' || bayar === 0) {
                teksKembalian.innerText = 'Rp 0';
                teksKembalian.className = 'fw-bold mb-0 text-secondary';
                document.getElementById('input-kembalian').value = 0;
                btnBayar.disabled = true;
                return;
            }

            // Uang bayar kurang
            if (bayar < total) {
                teksKembalian.innerText = 'Uang Kurang!';
                teksKembalian.className = 'fw-bold mb-0 text-danger';
                document.getElementById('input-kembalian').value = 0;
                btnBayar.disabled = true;
                return;
            }

            // Uang bayar cukup
            let fixKembali = kembali > 0 ? kembali : 0;
            teksKembalian.innerText = 'Rp ' + formatUang(fixKembali);
            teksKembalian.className = 'fw-bold mb-0 text-success';
            document.getElementById('input-kembalian').value = fixKembali;

            // Khusus QRIS: Pastikan bank sudah dipilih
            if (paymentMethod === 'qris') {
                let selectBank = document.getElementById('select-bank');
                let inputBankLain = document.getElementById('input-bank-lain');
                let bankVal = selectBank.value === 'Lainnya' ? inputBankLain.value.trim() : selectBank.value;
                if (!bankVal) {
                    btnBayar.disabled = true;
                    return;
                }
            }

            btnBayar.disabled = false;
        }

        // Toggle Payment Method (Cash vs QRIS)
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                let bankSection = document.getElementById('bank-section');
                let inputBayar = document.getElementById('input-bayar');
                let inputBayarDisplay = document.getElementById('input-bayar-display');
                let total = parseInt(document.getElementById('input-total').value) || 0;

                if (this.value === 'qris') {
                    bankSection.style.display = 'block';
                    inputBayar.value = total;
                    inputBayarDisplay.value = total > 0 ? formatUang(total) : '';
                    inputBayarDisplay.readOnly = true;
                } else {
                    bankSection.style.display = 'none';
                    inputBayar.value = 0;
                    inputBayarDisplay.value = '';
                    inputBayarDisplay.readOnly = false;
                    document.getElementById('select-bank').value = '';
                    document.getElementById('input-bank-lain').style.display = 'none';
                    document.getElementById('input-bank-lain').value = '';
                }
                kalkulasiUang();
            });
        });

        // Toggle Option Bank Lainnya
        document.getElementById('select-bank')?.addEventListener('change', function() {
            let inputLain = document.getElementById('input-bank-lain');
            if (this.value === 'Lainnya') {
                inputLain.style.display = 'block';
            } else {
                inputLain.style.display = 'none';
                inputLain.value = '';
            }
            kalkulasiUang();
        });

        document.getElementById('input-bank-lain')?.addEventListener('input', function() {
            kalkulasiUang();
        });

        // Form Submit Guard
        document.getElementById('form-kasir').addEventListener('submit', function(e) {
            let total = parseInt(document.getElementById('input-total').value) || 0;
            let bayar = parseInt(document.getElementById('input-bayar').value) || 0;
            let paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';

            if (keranjang.length === 0 || total === 0) {
                e.preventDefault();
                alert('Keranjang masih kosong!');
                return false;
            }
            if (bayar < total) {
                e.preventDefault();
                alert('Uang bayar masih kurang!');
                return false;
            }
            if (paymentMethod === 'qris') {
                let selectBank = document.getElementById('select-bank');
                let inputBankLain = document.getElementById('input-bank-lain');
                let bankVal = selectBank.value === 'Lainnya' ? inputBankLain.value.trim() : selectBank.value;
                if (!bankVal) {
                    e.preventDefault();
                    alert('Silakan pilih Bank / E-Wallet untuk pembayaran QRIS!');
                    return false;
                }
            }

            // Anti double-submit loading state
            let btn = document.getElementById('btn-bayar');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses Pembayaran...';
            isNavigatingForSubmit = true;
            hapusDraft();
        });

        window.addEventListener('beforeunload', function (event) {
            if (!isNavigatingForSubmit && keranjang.length > 0) {
                simpanDraft();
                event.preventDefault();
                event.returnValue = '';
            }
        });

        function closeSuccess() {
            let modal = document.getElementById('successModal');
            if (modal) {
                modal.style.display = 'none';
                document.getElementById('searchProduk')?.focus();
            }

        }

        function mulaiTransaksiBaru() {
            hapusDraft();
            resetTransaksi();
            closeSuccess();

            const tabPesanan = document.getElementById('pesanan-tab');
            if (window.bootstrap && bootstrap.Tab && tabPesanan) {
                bootstrap.Tab.getOrCreateInstance(tabPesanan).show();
            }

            renderLayar();
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && document.getElementById('successModal')?.style.display !== 'none') {
                closeSuccess();
            }
        });

        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.navigation && window.performance.navigation.type === 2)) {
                window.location.replace("{{ route('login') }}");
            }
        });
    </script>
</body>
</html>