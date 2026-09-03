<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'saldo_awal'];

    protected $casts = [
        'saldo_awal' => 'decimal:2',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Saldo kas berjalan = saldo awal + total pemasukan - total pengeluaran
     * yang melalui pos kas ini, sampai tanggal tertentu (default: hari ini).
     */
    public function saldoBerjalan(?string $sampaiTanggal = null): float
    {
        $sampaiTanggal = $sampaiTanggal ?? now()->toDateString();

        $pemasukan = $this->transactions()
            ->where('jenis', 'pemasukan')
            ->where('tanggal', '<=', $sampaiTanggal)
            ->sum('jumlah');

        $pengeluaran = $this->transactions()
            ->where('jenis', 'pengeluaran')
            ->where('tanggal', '<=', $sampaiTanggal)
            ->sum('jumlah');

        return (float) $this->saldo_awal + (float) $pemasukan - (float) $pengeluaran;
    }
}
