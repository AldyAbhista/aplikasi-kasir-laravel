<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data produk lama agar tidak bentrok
        DB::table('products')->truncate();

        // 1. Update password admin (tanpa hapus semua user)
        User::updateOrCreate(
            ['email' => 'admin@toko.com'],
            [
                'name' => 'Pemilik Toko',
                'password' => Hash::make('admin123'),
                'role' => 'admin' // Bebas akses semua
            ]
        );

        // 2. Hapus akun kasir tertentu
        User::where('email', 'kasir@toko.com')->delete();

        // 3. Hapus akun lainnya
        User::where('email', 'akun@contoh.com')->delete();

        // 3. Jalankan ProductSeeder
        $this->call(ProductSeeder::class);

        // 4. Tampilkan semua user
        App\Models\User::select('id','name','email','role')->get();
    }
}