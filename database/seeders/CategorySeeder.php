<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $pemasukan = [
            'Penjualan Makanan',
            'Penjualan Minuman',
            'Penjualan Produk Lain',
            'Pendapatan Lain-lain',
        ];

        $pengeluaran = [
            'Bahan Baku & Bahan Baku Kopi',
            'Gaji & Upah Karyawan',
            'Sewa Tempat',
            'Listrik, Air & Internet',
            'Perlengkapan & Kemasan',
            'Marketing & Promosi',
            'Perawatan Peralatan',
            'Lain-lain',
        ];

        foreach ($pemasukan as $name) {
            Category::firstOrCreate(['name' => $name, 'type' => 'pemasukan']);
        }

        foreach ($pengeluaran as $name) {
            Category::firstOrCreate(['name' => $name, 'type' => 'pengeluaran']);
        }
    }
}
