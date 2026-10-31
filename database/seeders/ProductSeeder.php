<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'kode_barang' => 'P001',
            'nama_barang' => 'Coca Cola 1L',
            'harga_beli' => 8000,
            'harga_jual' => 12000,
            'stok' => 50,
        ]);

        Product::create([
            'kode_barang' => 'P002',
            'nama_barang' => 'Sprite 1L',
            'harga_beli' => 8000,
            'harga_jual' => 12000,
            'stok' => 40,
        ]);

        Product::create([
            'kode_barang' => 'P003',
            'nama_barang' => 'Fanta Orange 1L',
            'harga_beli' => 7500,
            'harga_jual' => 11000,
            'stok' => 30,
        ]);

        Product::create([
            'kode_barang' => 'P004',
            'nama_barang' => 'Air Mineral 1.5L',
            'harga_beli' => 3000,
            'harga_jual' => 5000,
            'stok' => 100,
        ]);

        Product::create([
            'kode_barang' => 'P005',
            'nama_barang' => 'Indomie Mi Goreng',
            'harga_beli' => 2500,
            'harga_jual' => 4000,
            'stok' => 200,
        ]);

        Product::create([
            'kode_barang' => 'P006',
            'nama_barang' => 'Indomie Rebus',
            'harga_beli' => 2500,
            'harga_jual' => 4000,
            'stok' => 150,
        ]);

        Product::create([
            'kode_barang' => 'P007',
            'nama_barang' => 'Roti Tawar',
            'harga_beli' => 15000,
            'harga_jual' => 22000,
            'stok' => 20,
        ]);

        Product::create([
            'kode_barang' => 'P008',
            'nama_barang' => 'Coklat Delfi',
            'harga_beli' => 5000,
            'harga_jual' => 8000,
            'stok' => 80,
        ]);
    }
}
