<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal', 'category_id', 'account_id', 'jenis', 'jumlah', 'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function scopePeriode($query, ?string $dari, ?string $sampai)
    {
        if ($dari) {
            $query->whereDate('tanggal', '>=', $dari);
        }
        if ($sampai) {
            $query->whereDate('tanggal', '<=', $sampai);
        }

        return $query;
    }
}
