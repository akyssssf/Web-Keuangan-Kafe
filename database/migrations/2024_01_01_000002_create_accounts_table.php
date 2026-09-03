<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel "accounts" = pos-pos neraca (bukan rekening bank).
     * type:
     *  - kas        -> saldo dihitung otomatis dari transaksi (kas/bank operasional kafe)
     *  - aset_tetap -> mis. Peralatan Dapur, Kendaraan, Furnitur (saldo diinput manual)
     *  - kewajiban  -> mis. Hutang Supplier, Hutang Bank (saldo diinput manual)
     *  - modal      -> mis. Modal Pemilik (saldo diinput manual)
     *
     * saldo_awal dipakai sebagai titik awal perhitungan, khususnya untuk pos "kas".
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['kas', 'aset_tetap', 'kewajiban', 'modal']);
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
