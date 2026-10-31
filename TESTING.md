# 🧪 TESTING GUIDE - Aplikasi Kasir Multi-Tenant

## ✅ Pre-Testing Checklist

Sebelum mulai testing, pastikan:

```bash
# 1. Install dependencies
composer install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Database setup
touch database/database.sqlite

# 4. Run migration & seed
php artisan migrate
php artisan db:seed

# 5. Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 6. Start server
php artisan serve
```

---

## 🔐 Test 1: Login & Authentication

### Test 1.1: Admin Login
```
1. Buka http://localhost:8000/login
2. Masukkan:
   - Email: admin@toko.com
   - Password: admin123
3. Klik "Masuk"
```

**Expected Result:** ✅ Redirect ke `/admin` (Admin Dashboard)

### Test 1.2: Kasir Login
```
1. Buka http://localhost:8000/login
2. Masukkan:
   - Email: kasir@toko.com
   - Password: kasir123
3. Klik "Masuk"
```

**Expected Result:** ✅ Redirect ke `/kasir` (Halaman Kasir)

### Test 1.3: Invalid Login
```
1. Buka http://localhost:8000/login
2. Masukkan:
   - Email: admin@toko.com
   - Password: salah123
3. Klik "Masuk"
```

**Expected Result:** ❌ Error message "Email atau password yang Anda masukkan salah"

### Test 1.4: Register New User
```
1. Buka http://localhost:8000/register
2. Isi form:
   - Nama: Kasir Baru
   - Email: kasir.baru@toko.com
   - Password: kasir1234
   - Posisi: Pegawai Kasir (atau Admin)
3. Klik "Daftar Sekarang"
```

**Expected Result:** ✅ User baru tercatat, langsung login dengan akun baru

---

## 👤 Test 2: Admin Access Control

Login sebagai admin@toko.com terlebih dahulu.

### Test 2.1: Admin bisa akses Dashboard Admin
```
Akses: http://localhost:8000/admin
```
**Expected Result:** ✅ Halaman dashboard admin muncul dengan 3 menu:
- Buka Mesin Kasir
- Master Barang
- Laporan Keuangan

### Test 2.2: Admin bisa akses Halaman Kasir
```
Klik menu: "Buka Mesin Kasir"
atau: http://localhost:8000/kasir
```
**Expected Result:** ✅ Halaman kasir muncul dengan daftar produk

### Test 2.3: Admin bisa akses Master Barang
```
Klik menu: "Master Barang"
atau: http://localhost:8000/produk
```
**Expected Result:** ✅ Daftar produk muncul dengan tombol:
- Tambah Barang
- Edit (per produk)
- Hapus (per produk)

### Test 2.4: Admin bisa akses Laporan Keuangan
```
Klik menu: "Laporan Keuangan"
atau: http://localhost:8000/laporan
```
**Expected Result:** ✅ Tabel riwayat transaksi dengan:
- Tanggal
- ID Struk
- Uang Bayar
- Kembalian
- Total Belanja
- Tombol: Cetak Struk & Batalkan

### Test 2.5: Admin bisa Tambah Produk
```
Di Master Barang:
1. Klik "Tambah Barang"
2. Isi form:
   - Kode: P009
   - Nama: Coca Cola 2L
   - Harga Beli: 15000
   - Harga Jual: 22000
   - Stok: 25
3. Klik "Simpan"
```
**Expected Result:** ✅ Produk baru tercatat, halaman kembali ke list

### Test 2.6: Admin bisa Edit Produk
```
Di Master Barang:
1. Klik "Edit" pada produk
2. Ubah data (misal: stok jadi 30)
3. Klik "Update"
```
**Expected Result:** ✅ Data produk terupdate

### Test 2.7: Admin bisa Hapus Produk
```
Di Master Barang:
1. Klik "Hapus" pada produk
2. Konfirmasi hapus
```
**Expected Result:** ✅ Produk terhapus dari list

---

## 👤 Test 3: Kasir Access Control

Logout terlebih dahulu, kemudian login sebagai kasir@toko.com.

### Test 3.1: Kasir bisa akses Halaman Kasir
```
Akses: http://localhost:8000/kasir
```
**Expected Result:** ✅ Halaman kasir muncul dengan pilihan produk

### Test 3.2: Kasir TIDAK bisa akses Admin Dashboard
```
Akses: http://localhost:8000/admin
```
**Expected Result:** ❌ Redirect ke `/` (Home)

### Test 3.3: Kasir TIDAK bisa akses Master Barang
```
Akses: http://localhost:8000/produk
```
**Expected Result:** ❌ Redirect ke `/`

### Test 3.4: Kasir TIDAK bisa akses Laporan
```
Akses: http://localhost:8000/laporan
```
**Expected Result:** ❌ Redirect ke `/`

---

## 💳 Test 4: Transaction Flow

Login sebagai kasir@toko.com untuk test transaksi.

### Test 4.1: Kasir dapat membuat Transaksi
```
1. Di halaman kasir, klik produk (misal: Coca Cola 1L)
2. Produk masuk keranjang
3. Ulangi step 1-2 untuk produk lain
4. Lihat total di keranjang terupdate otomatis
```
**Expected Result:** ✅ Produk masuk keranjang, total harga terupdate

### Test 4.2: Kasir dapat mengubah Jumlah
```
Di keranjang:
1. Klik tombol "+" untuk tambah jumlah
2. Klik tombol "-" untuk kurangi
3. Klik "X" untuk hapus produk
```
**Expected Result:** ✅ Jumlah berubah, total terupdate

### Test 4.3: Hitung Kembalian Otomatis
```
1. Total tagihan: Rp 25.000
2. Masukkan "Uang Bayar": 50.000
3. Kembalian otomatis: Rp 25.000
```
**Expected Result:** ✅ Kembalian dihitung otomatis saat input uang

### Test 4.4: Proses Pembayaran
```
1. Setelah isi uang bayar
2. Klik "Proses Pembayaran"
3. Tunggu proses
```
**Expected Result:** ✅ Success message muncul + tombol "Cetak Struk"

### Test 4.5: Cetak Struk
```
Setelah pembayaran berhasil:
1. Klik "Cetak Struk"
2. Halaman struk terbuka
```
**Expected Result:** ✅ Format struk muncul dengan detail transaksi

---

## 📊 Test 5: Laporan & Audit Trail

Login sebagai admin@toko.com.

### Test 5.1: Laporan menampilkan Transaksi
```
1. Ke Laporan Keuangan
2. Lihat riwayat transaksi dari kasir
```
**Expected Result:** ✅ Transaksi yang dibuat kasir muncul di laporan

### Test 5.2: Tracking User di Transaksi
```
Buka database:
SELECT transactions.*, users.name 
FROM transactions 
JOIN users ON transactions.user_id = users.id;
```
**Expected Result:** ✅ user_id tercatat, nama kasir bisa di-join

### Test 5.3: Admin dapat Void Transaksi
```
Di Laporan:
1. Klik "Batalkan" pada transaksi
2. Konfirmasi
```
**Expected Result:** ✅ Transaksi terhapus, stok produk dikembalikan

### Test 5.4: Void Transaksi mengembalikan Stok
```
1. Cek stok produk sebelum transaksi
2. Lakukan transaksi
3. Cek stok berkurang
4. Batalkan transaksi
5. Cek stok kembali ke semula
```
**Expected Result:** ✅ Stok berkurang & kembali dengan benar

---

## 🔒 Test 6: Security & Authorization

### Test 6.1: Middleware Role Protection
```
Test scenario:
- Login kasir, coba akses /admin → Ditolak
- Login kasir, coba akses /produk → Ditolak
- Login admin, akses semua → Diizinkan
```
**Expected Result:** ✅ Middleware role berfungsi dengan baik

### Test 6.2: Session Management
```
1. Login admin
2. Buka tab baru, akses /kasir
3. Klik Logout di tab pertama
4. Refresh tab kedua
```
**Expected Result:** ✅ Session terhapus, redirect ke login

### Test 6.3: Password Hashing
```
Cek database:
SELECT email, password FROM users;
```
**Expected Result:** ✅ Password adalah hash (bukan plaintext)

---

## ⚙️ Test 7: Database Integrity

### Test 7.1: Foreign Key Constraint
```
Coba delete user yang punya transaksi:
DELETE FROM users WHERE id = 1;
```
**Expected Result:** ✅ user_id di transactions menjadi NULL (onDelete set null)

### Test 7.2: Transaction Cascade Delete
```
Jalankan void transaksi dan cek:
SELECT * FROM transaction_details 
WHERE transaction_id = <deleted_id>;
```
**Expected Result:** ✅ Detail transaksi otomatis terhapus

---

## 📱 Test 8: User Experience

### Test 8.1: UI Responsiveness
```
Test di berbagai ukuran layar:
- Desktop (1920px)
- Tablet (768px)
- Mobile (375px)
```
**Expected Result:** ✅ Layout responsif di semua ukuran

### Test 8.2: Error Handling
```
Test berbagai error scenario:
- Input field kosong
- Input karakter spesial
- Stok produk habis
```
**Expected Result:** ✅ Error message user-friendly ditampilkan

### Test 8.3: Navigation
```
Test navigasi antar halaman:
- Admin dashboard → Master barang ✅
- Master barang → Laporan ✅
- Laporan → Kasir ✅
```
**Expected Result:** ✅ Navigasi smooth tanpa error

---

## 📋 Test Results Template

Jika semua test lulus, isi template ini:

```
┌─────────────────────────────────────┐
│ ✅ TESTING COMPLETED SUCCESSFULLY  │
├─────────────────────────────────────┤
│ Test 1: Login & Auth ........... ✅  │
│ Test 2: Admin Access .......... ✅  │
│ Test 3: Kasir Access ......... ✅  │
│ Test 4: Transaction ........... ✅  │
│ Test 5: Laporan & Audit ....... ✅  │
│ Test 6: Security .............. ✅  │
│ Test 7: Database .............. ✅  │
│ Test 8: UX .................... ✅  │
├─────────────────────────────────────┤
│ Total: 8/8 Test Passed       100%  │
└─────────────────────────────────────┘
```

---

## 🐛 Bug Report Template

Jika menemukan bug, report dengan format ini:

```markdown
### Bug #X: [Short Description]

**Severity:** [Critical/High/Medium/Low]
**Affected User:** [Admin/Kasir/Both]

**Steps to Reproduce:**
1. ...
2. ...
3. ...

**Expected Result:**
...

**Actual Result:**
...

**Screenshots/Logs:**
...
```

---

## 🎯 Success Criteria

Aplikasi siap production jika:

- ✅ Semua 8 test lulus
- ✅ Tidak ada error di console
- ✅ Database queries efficient
- ✅ Role-based access bekerja
- ✅ User tracking tercatat
- ✅ UI responsive
- ✅ Transaksi & void berfungsi
- ✅ Laporan akurat

---

**Happy Testing! 🚀**
