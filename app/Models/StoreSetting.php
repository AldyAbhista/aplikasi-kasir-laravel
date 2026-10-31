<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'address',
        'phone',
        'footer_message',
        'qris_enabled',
        'qris_image',
    ];

    protected function casts(): array
    {
        return [
            'qris_enabled' => 'boolean',
        ];
    }

    /**
     * Helper static untuk mengambil setting toko tunggal
     */
    public static function getSetting(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'store_name' => 'Kasir',
                'address' => 'Jl. Contoh Alamat No. 123',
                'phone' => '08123456789',
                'footer_message' => 'Terima Kasih! Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan.',
            ]
        );
    }
}
