<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_name',
        'no_nota',
        'total_harga',
        'tax_rate',
        'tax_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'bayar',
        'kembalian',
        'payment_method',
        'order_type',
        'bank',
        'notes',
        'queue_number',
    ];

    /**
     * Accessor untuk No Nota harian (contoh: TRX-20260813-001)
     */
    public function getNoNotaAttribute(): string
    {
        if (! empty($this->attributes['no_nota'])) {
            return $this->attributes['no_nota'];
        }
        $date = $this->created_at ? $this->created_at->format('Ymd') : now()->format('Ymd');
        $queue = $this->queue_number ?? $this->id;

        return sprintf('TRX-%s-%03d', $date, $queue);
    }

    /**
     * Relasi ke User - transaksi milik user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke TransactionDetail - transaksi punya banyak detail
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
