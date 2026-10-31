<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'jumlah',
        'harga_satuan',
        'harga_beli',
        'subtotal',
        'notes',
    ];

    /**
     * Relasi ke Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}