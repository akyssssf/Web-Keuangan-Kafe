<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $bulanIni = now()->startOfMonth()->toDateString();
        $hariIni = now()->toDateString();

        $pemasukanBulanIni = Transaction::where('jenis', 'pemasukan')
            ->whereDate('tanggal', '>=', $bulanIni)
            ->sum('jumlah');

        $pengeluaranBulanIni = Transaction::where('jenis', 'pengeluaran')
            ->whereDate('tanggal', '>=', $bulanIni)
            ->sum('jumlah');

        $labaBulanIni = $pemasukanBulanIni - $pengeluaranBulanIni;

        $totalKas = Account::where('type', 'kas')->get()
            ->sum(fn ($account) => $account->saldoBerjalan($hariIni));

        $transaksiTerbaru = Transaction::with(['category', 'account'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        // Data grafik 7 hari terakhir
        $grafik = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i)->toDateString();
            $grafik[] = [
                'tanggal' => $tanggal,
                'label' => now()->subDays($i)->translatedFormat('d M'),
                'pemasukan' => (float) Transaction::where('jenis', 'pemasukan')->whereDate('tanggal', $tanggal)->sum('jumlah'),
                'pengeluaran' => (float) Transaction::where('jenis', 'pengeluaran')->whereDate('tanggal', $tanggal)->sum('jumlah'),
            ];
        }

        $grafik = collect($grafik);

        return view('dashboard.index', compact(
            'pemasukanBulanIni',
            'pengeluaranBulanIni',
            'labaBulanIni',
            'totalKas',
            'transaksiTerbaru',
            'grafik'
        ));
    }
}
