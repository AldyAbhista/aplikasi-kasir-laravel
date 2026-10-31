# ⚡ QUICK START - Aplikasi Kasir v2.1 (Laporan Keuangan Lengkap)

## 🎯 Apa yang Baru?

Aplikasi kasir Anda sekarang memiliki:
- ✅ Dashboard keuangan dengan grafik
- ✅ Laporan bulanan lengkap
- ✅ Edit transaksi
- ✅ Tracking profit/keuntungan
- ✅ Export laporan ke Excel

---

## 🚀 Instalasi (3 Langkah Mudah)

### Step 1: Update Database
Jalankan di terminal aplikasi:
```bash
php artisan migrate
```

Ini akan menambah kolom `harga_beli` untuk tracking profit.

### Step 2: Clear Cache
```bash
php artisan optimize:clear
php artisan config:clear
```

### Step 3: Restart Server
```bash
php artisan serve
```

**Aplikasi siap di http://localhost:8000** 🎉

---

## 📱 Cara Menggunakan

### Admin Login
```
Email: admin@toko.com
Password: admin123
```

### 1️⃣ Buat Transaksi
1. Login admin atau kasir
2. Klik "Buka Mesin Kasir"
3. Pilih produk & tentukan jumlah
4. Hitung kembalian otomatis
5. Proses pembayaran

### 2️⃣ Lihat Dashboard Keuangan
1. Login admin
2. Di Dashboard Admin → Klik "Laporan Keuangan"
3. Lihat statistik:
   - Total pendapatan
   - Total modal barang
   - Total keuntungan
   - Grafik 12 bulan
   - Top 5 produk

### 3️⃣ Lihat Laporan Bulanan
1. Di Dashboard Keuangan → Klik "Laporan Detail Bulanan"
2. Pilih bulan/tahun yang ingin dilihat
3. Lihat semua transaksi dengan detail profit
4. Edit atau hapus transaksi jika perlu
5. Export ke CSV atau Print

### 4️⃣ Edit Transaksi
1. Di Laporan Bulanan → Cari transaksi
2. Klik tombol "Edit" (pencil icon)
3. Ubah jumlah bayar jika salah input
4. Kembalian otomatis ter-update
5. Klik "Simpan Perubahan"

### 5️⃣ Batalkan Transaksi
1. Di Laporan Bulanan → Cari transaksi
2. Klik tombol "Hapus" (trash icon)
3. Konfirmasi penghapusan
4. ✅ Stok barang otomatis dikembalikan

### 6️⃣ Export Laporan
1. Di Laporan Bulanan
2. Klik "Export CSV"
3. File otomatis download
4. Buka di Excel untuk analisis lebih lanjut

---

## 📊 Dashboard Keuangan

### Kartu Statistik (Top Row)
```
┌──────────────────┬──────────────┬─────────────────┬──────────────┐
│ Total Pendapatan │ Total Modal  │ Total Keuntungan │ Transaksi    │
│ Rp 5,000,000     │ Rp 3,000,000 │ Rp 2,000,000    │ 150 transaksi│
└──────────────────┴──────────────┴─────────────────┴──────────────┘
```

### Grafik Pendapatan 12 Bulan
Lihat tren pendapatan setiap bulan dalam satu diagram

### Top 5 Produk
Produk apa yang paling sering dijual?

---

## 💰 Kalkulasi Profit

Sistem otomatis menghitung:

```
Profit per Item = (Harga Jual - Harga Beli) × Jumlah

Contoh:
- Coca Cola: Beli Rp 8.000, Jual Rp 12.000, qty 10
- Profit = (12.000 - 8.000) × 10 = Rp 40.000
```

**Tampilan di Laporan:**
- Per transaksi → lihat profit yang diterima
- Per bulan → total keuntungan bulanan
- Margin % → berapa persen profit dari penjualan

---

## 🔍 Laporan Bulanan Detail

### Filter & Action
1. **Pilih Bulan/Tahun** → Data auto-refresh
2. **Export CSV** → Download ke Excel
3. **Print** → Print laporan

### Kolom Tabel
| Kolom | Arti |
|-------|------|
| Tanggal | Kapan transaksi dilakukan |
| Kasir | Siapa yang melakukan transaksi |
| Total Barang | Berapa item/line di transaksi itu |
| Total Penjualan | Total harga jual |
| Modal | Total harga beli barang |
| Keuntungan | Profit yang didapat |

### Detail Barang
Dibawah setiap transaksi, lihat detail barang yang dijual:
```
• Coca Cola (2x @ Rp 12.000) = Rp 24.000
• Sprite (1x @ Rp 12.000) = Rp 12.000
```

---

## 🎯 Use Cases

### Kasir Ingin Lihat Transaksinya
- Login sebagai kasir
- Hanya bisa akses halaman kasir (transaksi)
- Tidak bisa lihat laporan keuangan

### Admin Ingin Monitoring Harian
1. Login sebagai admin
2. Buka Dashboard Keuangan
3. Lihat statistik hari ini
4. Check performa kasir

### Pemilik Ingin Analisis Bulanan
1. Login admin
2. Buka Laporan Bulanan
3. Filter bulan tertentu
4. Lihat total profit bulanan
5. Export ke Excel untuk analisis lebih detail

### Ada Kesalahan Transaksi
1. Admin buka Laporan Bulanan
2. Cari transaksi yang salah
3. Klik Edit
4. Update jumlah bayar
5. Simpan

### Produk Rusak/Kadaluarsa Perlu Batalkan
1. Admin buka Laporan
2. Cari transaksi
3. Klik Hapus
4. Konfirmasi
5. Stok otomatis dikembalikan ke etalase

---

## 📋 Checklist Setelah Update

- [ ] Jalankan `php artisan migrate`
- [ ] Jalankan `php artisan optimize:clear`
- [ ] Restart server
- [ ] Login admin
- [ ] Buat 1-2 test transaksi
- [ ] Buka Dashboard Keuangan
- [ ] Lihat laporan bulanan
- [ ] Coba edit transaksi
- [ ] Coba batalkan transaksi
- [ ] Export laporan

---

## ❓ FAQ

**Q: Bisakah kasir lihat laporan keuangan?**
A: Tidak. Kasir hanya bisa akses halaman kasir (transaksi). Laporan hanya untuk admin.

**Q: Bagaimana jika salah input transaksi?**
A: Admin bisa edit transaksi di laporan → ubah uang bayar → simpan.

**Q: Bagaimana jika produk hilang/rusak?**
A: Admin batalkan transaksi di laporan → stok otomatis dikembalikan.

**Q: Bagaimana profit dihitung?**
A: Profit = Harga Jual - Harga Beli. Sistem otomatis hitung untuk setiap item.

**Q: Bisakah export laporan?**
A: Ya, di laporan bulanan ada tombol "Export CSV" untuk download ke Excel.

**Q: Bisakah print laporan?**
A: Ya, ada tombol "Print" untuk print langsung ke printer.

**Q: Data lama bisa dilihat?**
A: Ya, filter laporan per bulan untuk lihat data bulan-bulan sebelumnya.

---

## 🆘 Troubleshooting

### Error: "Class ReportController not found"
```bash
php artisan config:clear
php artisan serve
```

### Laporan tidak muncul
```bash
php artisan cache:clear
php artisan view:clear
php artisan migrate
```

### Harga beli tidak terlihat
- Buat transaksi baru (otomatis track harga beli)
- Data lama mungkin tidak ada harga beli (bisa edit manual)

---

## 📞 Next Steps

1. **Update database** dengan `php artisan migrate`
2. **Test aplikasi** dengan login admin/kasir
3. **Buat transaksi test**
4. **Lihat laporan** untuk verifikasi
5. **Baca dokumentasi lengkap** di `UPDATE_v2.1.md`

---

**Selamat! Aplikasi kasir Anda sekarang memiliki fitur laporan profesional seperti aplikasi kasir pada umumnya.** 🎉

**Versi:** 2.1.0  
**Status:** Ready for Production ✅
