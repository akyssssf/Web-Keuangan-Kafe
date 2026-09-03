<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel kategori transaksi.
     * type: 'pemasukan' (mis. Penjualan Makanan, Penjualan Minuman)
     *       'pengeluaran' (mis. Bahan Baku, Gaji, Sewa, Listrik & Air)
     * Kategori ini yang dipakai untuk menyusun Laporan Laba Rugi.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['pemasukan', 'pengeluaran']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
