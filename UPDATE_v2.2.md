# 💳 Fitur Metode Pembayaran (Payment Method) - v2.2

## 🎯 Apa yang Ditambahkan?

Update v2.2 menambahkan fitur metode pembayaran untuk tracking lebih detail:

### ✨ Fitur Baru:
- ✅ Pilih metode pembayaran: **Cash** atau **QRIS**
- ✅ Untuk QRIS: Pilih bank dari list atau ketik custom
- ✅ Track pembayaran di laporan keuangan
- ✅ Breakdown cash vs QRIS di dashboard
- ✅ Detail bank untuk transaksi QRIS
- ✅ Metode pembayaran muncul di struk

---

## 🗄️ Database Changes

### Migration: 2026_04_22_150000_add_payment_method_to_transactions

**Kolom Baru di `transactions` table:**
```
- payment_method: string (default: 'cash') → cash atau qris
- bank: string nullable → Nama bank/e-wallet untuk QRIS
```

**Contoh Data:**
```
| ID | payment_method | bank     | bayar    |
|----|----------------|----------|----------|
| 1  | cash           | NULL     | 50000    |
| 2  | qris           | BCA      | 75000    |
| 3  | qris           | DANA     | 125000   |
| 4  | cash           | NULL     | 200000   |
```

---

## 💻 Frontend Changes

### Halaman Kasir (kasir/index.blade.php)

**Tambahan Komponen:**

1. **Metode Pembayaran Radio Button**
   - Cash: 💵 `<i class="bi bi-cash-coin"></i>`
   - QRIS: 📱 `<i class="bi bi-phone"></i>`

2. **Bank Selector untuk QRIS**
   - Dropdown dengan opsi: BCA, Mandiri, BRI, Permata, OVO, DANA, Gopay, LinkAja, Lainnya
   - Input text untuk custom bank jika pilih "Lainnya"
   - Hanya muncul saat QRIS dipilih

**JavaScript Logic:**
```javascript
// Toggle bank section ketika QRIS dipilih
if (paymentMethod === 'qris') {
    bankSection.style.display = 'block';
} else {
    bankSection.style.display = 'none';
}

// Validasi bank sebelum submit
if (paymentMethod === 'qris' && !finalBank) {
    alert('Pilih atau ketik nama bank/e-wallet');
}
```

---

## 🎨 UI/UX pada Halaman Kasir

### Tampilan Form:

```
┌─────────────────────────────────────┐
│ Metode Pembayaran                   │
├─────────────────────────────────────┤
│  [💵 CASH]    [📱 QRIS]             │
│                                     │
│ Bank/E-Wallet (tampil jika QRIS)    │
│ ┌─────────────────────────────────┐ │
│ │ -- Pilih Bank --                │ │
│ │ BCA                             │ │
│ │ Mandiri                         │ │
│ │ DANA                            │ │
│ │ Lainnya...                      │ │
│ └─────────────────────────────────┘ │
│                                     │
│ Input Custom (tampil jika Lainnya)  │
│ ┌─────────────────────────────────┐ │
│ │ Ketik nama bank/e-wallet        │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```

---

## 📊 Dashboard Keuangan (laporan/dashboard.blade.php)

### Fitur Baru:

**Payment Method Breakdown Cards:**
```
┌──────────────────────┬──────────────────────┐
│  💵 Pembayaran Cash   │  📱 Pembayaran QRIS  │
├──────────────────────┼──────────────────────┤
│ Total Pendapatan:    │ Total Pendapatan:    │
│ Rp 500.000           │ Rp 750.000           │
│                      │                      │
│ Transaksi: 10        │ Transaksi: 15        │
└──────────────────────┴──────────────────────┘
```

**Detail Bank QRIS:**
```
┌─────────────────────────────────────┐
│ 🏦 Detail Bank QRIS                 │
├─────────────────────────────────────┤
│ Bank          | Transaksi | Pending │
│ BCA           | 5         | 150.000 │
│ DANA          | 3         | 75.000  │
│ Mandiri       | 7         | 525.000 │
│ OVO           | 2         | 50.000  │
└─────────────────────────────────────┘
```

**Data Dikirim ke View:**
- `$cash_revenue` - Total pendapatan cash
- `$qris_revenue` - Total pendapatan QRIS
- `$cash_count` - Jumlah transaksi cash
- `$qris_count` - Jumlah transaksi QRIS
- `$bank_breakdown` - Detail per bank

---

## 📋 Laporan Bulanan (laporan/monthly.blade.php)

### Tambahan:

1. **Payment Breakdown Cards** (per bulan)
   - Cash stats
   - QRIS stats

2. **Kolom Baru di Tabel Transaksi:**
   - Nama: "Pembayaran"
   - Konten: Cash badge atau QRIS + bank name
   - Contoh: 
     - ✅ `<span class="badge bg-success">💵 Cash</span>`
     - ✅ `<span class="badge bg-info">📱 QRIS BCA</span>`

**Data Dikirim ke View:**
- `$payment_breakdown` - Breakdown per metode untuk bulan ini

---

## 📄 Edit Transaksi (laporan/edit.blade.php)

### Tambahan di Transaction Summary:

```
ID Struk: #TRX-5
Tanggal: 22/04/2026 14:30
Kasir: Pegawai Kasir
Total Item: 3
Status: Selesai
Metode Pembayaran: 📱 QRIS DANA
```

---

## 🧾 Struk (kasir/struk.blade.php)

### Perubahan:

**Sebelum:**
```
TOTAL              Rp 100.000
Tunai              Rp 100.000
Kembali            Rp 0
```

**Sesudah:**
```
TOTAL              Rp 100.000
QRIS BCA           Rp 100.000
Kembali            Rp 0
```

atau

```
TOTAL              Rp 100.000
Tunai              Rp 150.000
Kembali            Rp 50.000
```

---

## 🔧 Controller Changes

### TransactionController.php

**Validasi Tambahan:**
```php
'payment_method' => 'required|in:cash,qris',
'bank' => 'nullable|string',
```

**Simpan Data:**
```php
Transaction::create([
    ...
    'payment_method' => $request->payment_method,  // 'cash' atau 'qris'
    'bank' => $request->bank,                       // Nama bank untuk QRIS
]);
```

### ReportController.php

**Dashboard Method:**
```php
// Breakdown cash vs QRIS
$cash_revenue = Transaction::where('payment_method', 'cash')->sum('total_harga');
$qris_revenue = Transaction::where('payment_method', 'qris')->sum('total_harga');
$cash_count = Transaction::where('payment_method', 'cash')->count();
$qris_count = Transaction::where('payment_method', 'qris')->count();

// Detail bank untuk QRIS
$bank_breakdown = Transaction::where('payment_method', 'qris')
    ->selectRaw('bank, COUNT(*) as count, SUM(total_harga) as revenue')
    ->groupBy('bank')
    ->get();
```

**Monthly Method:**
```php
// Breakdown metode pembayaran per bulan
$payment_breakdown = Transaction::whereMonth('created_at', $month)
    ->whereYear('created_at', $year)
    ->selectRaw('payment_method, COUNT(*) as count, SUM(total_harga) as revenue')
    ->groupBy('payment_method')
    ->get();
```

---

## 📊 Analisis Pembayaran

### Query Contoh untuk Reporting:

**1. Total pendapatan per metode pembayaran:**
```php
$payment_stats = Transaction::selectRaw('payment_method, COUNT(*) as count, SUM(total_harga) as revenue')
    ->groupBy('payment_method')
    ->get();
```

**2. Detail bank QRIS dengan total transaksi:**
```php
$bank_stats = Transaction::where('payment_method', 'qris')
    ->selectRaw('bank, COUNT(*) as count, SUM(total_harga) as revenue, AVG(total_harga) as avg_value')
    ->groupBy('bank')
    ->orderByDesc('revenue')
    ->get();
```

**3. Perbandingan cash vs QRIS per bulan:**
```php
$monthly_payment = Transaction::selectRaw('MONTH(created_at) as month, payment_method, SUM(total_harga) as revenue')
    ->whereYear('created_at', now()->year)
    ->groupBy('month', 'payment_method')
    ->get();
```

---

## 🧪 Testing Checklist

- [ ] Buat transaksi dengan payment method Cash
- [ ] Buat transaksi dengan payment method QRIS BCA
- [ ] Buat transaksi dengan payment method QRIS custom (ketik bank name)
- [ ] Cek di dashboard → Cash vs QRIS breakdown benar
- [ ] Cek di dashboard → Bank detail QRIS detail
- [ ] Cek di laporan bulanan → Payment breakdown cards benar
- [ ] Cek di laporan bulanan → Tabel transaksi menampilkan payment method
- [ ] Cek di edit transaksi → Payment method terlihat di summary
- [ ] Cek struk → Payment method ditampilkan dengan benar
- [ ] Export CSV → Berisi payment method info (jika implemented)

---

## 🎯 Use Cases

### Admin Ingin Tahu Berapa Pendapatan Cash vs QRIS:
1. Login admin
2. Buka Dashboard Keuangan
3. Lihat kartu "Pembayaran Cash" dan "Pembayaran QRIS"
4. Lihat detail bank untuk QRIS

### Admin Ingin Laporan Detail Bank QRIS:
1. Buka Dashboard Keuangan
2. Scroll ke "Detail Bank QRIS"
3. Lihat breakdown per bank dengan jumlah transaksi dan total

### Admin Ingin Filter Laporan Bulanan per Metode Pembayaran:
1. Buka Laporan Bulanan
2. Filter bulan yang ingin dilihat
3. Lihat "Payment Breakdown Cards" untuk stats cash vs QRIS
4. Lihat di tabel kolom "Pembayaran" untuk detail setiap transaksi

### Kasir Input Pembayaran QRIS:
1. Pilih produk seperti biasa
2. Di halaman kasir → Klik tombol "QRIS"
3. Dropdown muncul → Pilih bank (contoh: BCA)
4. Input uang bayar (untuk QRIS biasanya sama dengan total)
5. Proses pembayaran → Tersimpan sebagai QRIS BCA

---

## 📈 Future Enhancements

Fitur yang bisa ditambah di masa depan:
- [ ] Analisis trend cash vs QRIS over time
- [ ] Biaya admin per metode pembayaran
- [ ] Reconciliation QRIS dengan laporan bank
- [ ] Commission per bank (untuk QRIS)
- [ ] Split payment (cash + QRIS kombinasi)
- [ ] Manual reconciliation tools

---

## 🔄 Data Migration untuk Historical Data

Untuk transaksi lama yang tidak memiliki payment_method:
```php
// Set semua transaksi lama sebagai cash (default)
Transaction::whereNull('payment_method')->update(['payment_method' => 'cash']);
```

---

## 📝 Summary of Changes

| File | Perubahan |
|------|----------|
| Migration | ✨ Tambah payment_method & bank |
| Transaction Model | ♻️ Add fillable fields |
| TransactionController | ♻️ Validate & store payment method |
| ReportController | ♻️ Add payment breakdown queries |
| kasir/index.blade.php | ✨ Add payment method selector |
| kasir/struk.blade.php | ♻️ Show payment method |
| laporan/dashboard.blade.php | ✨ Add payment breakdown cards |
| laporan/monthly.blade.php | ✨ Add payment breakdown & table column |
| laporan/edit.blade.php | ♻️ Show payment method info |

---

**Versi:** 2.2.0  
**Release Date:** 22 April 2026  
**Status:** Ready for Testing ✅
