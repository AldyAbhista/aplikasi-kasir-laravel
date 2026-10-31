# 🚀 PANDUAN INSTALASI DAN SETUP APLIKASI KASIR

## 📋 Prasyarat

- PHP 8.2+
- Composer
- SQLite atau MySQL
- Git (optional)

---

## ⚡ Quick Start (Recommended)

### 1. Install Dependencies
```bash
composer install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup
```bash
# Option A: SQLite (Default)
touch database/database.sqlite

# Option B: MySQL
# Edit .env:
# DB_DATABASE=nama_database
# DB_USERNAME=root
# DB_PASSWORD=password
```

### 4. Run Migrations & Seeds
```bash
# Jalankan semua migration
php artisan migrate

# Isi data testing (2 user + 8 produk)
php artisan db:seed
```

### 5. Clear Cache & Optimize
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan optimize:clear
```

### 6. Start Development Server
```bash
php artisan serve
```

**Aplikasi siap diakses di:** `http://localhost:8000`

---

## 🔐 Login Testing

### Admin Account
```
Email: admin@toko.com
Password: admin123
```
**Akses ke:** Dashboard Admin → Kasir, Master Barang, Laporan Keuangan

### Kasir Account
```
Email: kasir@toko.com
Password: kasir123
```
**Akses ke:** Hanya Halaman Kasir (Transaksi Penjualan)

---

## 📱 Apa yang Bisa Dilakukan

### ✅ Admin
- Dashboard dengan 3 menu utama
- **Kasir**: Buka mesin kasir dan lakukan transaksi
- **Master Barang**: Kelola stok produk (tambah, edit, hapus)
- **Laporan Keuangan**: Lihat riwayat transaksi dan total pendapatan
- Void transaksi (batalkan penjualan dan kembalikan stok)

### ✅ Kasir
- Melakukan transaksi penjualan
- Memilih produk dan tentukan jumlah
- Hitung kembalian otomatis
- Cetak struk penjualan

---

## 📁 Struktur Folder Penting

```
aplikasi-kasir/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Logic aplikasi
│   │   └── Middleware/       # Role checking
│   └── Models/               # Database models
├── database/
│   ├── migrations/           # SQL schema
│   └── seeders/              # Data testing
├── resources/views/          # HTML templates
│   ├── admin/               # Admin views
│   ├── kasir/               # Kasir views
│   ├── products/            # Product views
│   └── auth/                # Login/Register views
├── routes/
│   └── web.php              # URL routes
├── bootstrap/
│   └── app.php              # Middleware registration
└── .env                     # Konfigurasi aplikasi
```

---

## 🆘 Troubleshooting

### Error: "No application encryption key has been specified"
```bash
php artisan key:generate
```

### Error: "Class DatabaseSeeder does not exist"
```bash
php artisan db:seed --class=Database\\Seeders\\DatabaseSeeder
```

### Error: Database connection failed
- Periksa `.env` DB_CONNECTION dan DB_DATABASE
- Pastikan database sudah dibuat (MySQL)
- Pastikan `database/database.sqlite` exist (SQLite)

### Middleware 'role' not found
- Restart server: `php artisan serve`
- Clear config: `php artisan config:clear`

### Migrations tidak jalan
```bash
# Reset semua migration dan jalankan ulang
php artisan migrate:refresh --seed
```

### HATI-HATI: Ini akan menghapus semua data!

---

## 🔄 Development Commands

```bash
# Buat controller baru
php artisan make:controller NameController

# Buat model baru
php artisan make:model NameModel

# Buat migration baru
php artisan make:migration create_table_name

# Buat seeder baru
php artisan make:seeder NameSeeder

# Lihat semua routes
php artisan route:list

# Optimize aplikasi
php artisan optimize
```

---

## 🎯 Next Steps

1. **Tambah User Manual**
   - Buka http://localhost:8000/register
   - Isi form (pilih role: admin atau kasir)
   - Login dengan akun baru

2. **Manage Produk** (Admin)
   - Login sebagai admin
   - Ke Master Barang
   - Tambah/Edit/Hapus produk

3. **Test Transaksi** (Kasir)
   - Login sebagai kasir
   - Buka Halaman Kasir
   - Pilih produk dan proses pembayaran

4. **Lihat Laporan** (Admin)
   - Login sebagai admin
   - Ke Laporan Keuangan
   - Lihat riwayat dan total pemasukan

---

## 📊 Database Schema

### Users Table
```sql
id | name | email | password | role | created_at | updated_at
```

### Products Table
```sql
id | kode_barang | nama_barang | harga_beli | harga_jual | stok | created_at | updated_at
```

### Transactions Table
```sql
id | user_id | total_harga | bayar | kembalian | created_at | updated_at
```

### Transaction Details Table
```sql
id | transaction_id | product_id | jumlah | harga_satuan | subtotal | created_at | updated_at
```

---

## 🛡️ Security Tips

1. **Ubah password default** setelah instalasi
2. **Jangan share `.env`** file (sensitive data)
3. **Update Laravel** secara berkala
4. **Use HTTPS** di production
5. **Backup database** secara regular

---

## 📞 Support

Jika ada pertanyaan atau error:
1. Cek dokumentasi: PERBAIKAN_MULTI_TENANT.md
2. Cek error message di terminal
3. Cek Laravel documentation: https://laravel.com/docs

---

**Selamat! Aplikasi Kasir Anda sudah siap digunakan.** 🎉
