<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    // Ini adalah 'izin' agar kolom-kolom ini boleh diisi data dari formulir
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'harga_beli',
        'harga_jual',
        'stok',
    ];
}