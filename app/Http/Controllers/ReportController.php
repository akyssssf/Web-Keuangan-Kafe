<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * LAPORAN LABA RUGI
     * Membandingkan total pemasukan vs pengeluaran per kategori pada suatu periode.
     */
    public function labaRugi(Request $request)
    {
        $dari = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->toDateString());

        $pemasukanPerKategori = Transaction::with('category')
            ->where('jenis', 'pemasukan')
            ->periode($dari, $sampai)
            ->get()
            ->groupBy('category.name')
            ->map(fn ($rows) => $rows->sum('jumlah'))
            ->sortDesc();

        $pengeluaranPerKategori = Transaction::with('category')
            ->where('jenis', 'pengeluaran')
            ->periode($dari, $sampai)
            ->get()
            ->groupBy('category.name')
            ->map(fn ($rows) => $rows->sum('jumlah'))
            ->sortDesc();

        $totalPemasukan = $pemasukanPerKategori->sum();
        $totalPengeluaran = $pengeluaranPerKategori->sum();
        $labaRugiBersih = $totalPemasukan - $totalPengeluaran;

        return view('reports.laba-rugi', compact(
            'dari', 'sampai',
            'pemasukanPerKategori', 'pengeluaranPerKategori',
            'totalPemasukan', 'totalPengeluaran', 'labaRugiBersih'
        ));
    }

    /**
     * LAPORAN ARUS KAS
     * Menunjukkan pergerakan kas masuk & keluar per hari pada suatu periode,
     * plus saldo kas awal & akhir periode.
     */
    public function arusKas(Request $request)
    {
        $dari = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->toDateString());

        $akunKas = Account::where('type', 'kas')->get();
        $saldoAwalPeriode = $akunKas->sum(fn ($a) => $a->saldoBerjalan(
            \Illuminate\Support\Carbon::parse($dari)->subDay()->toDateString()
        ));

        $rows = Transaction::whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal')
            ->get()
            ->groupBy(fn ($t) => $t->tanggal->toDateString())
            ->map(function ($items, $tanggal) {
                return [
                    'tanggal' => $tanggal,
                    'pemasukan' => $items->where('jenis', 'pemasukan')->sum('jumlah'),
                    'pengeluaran' => $items->where('jenis', 'pengeluaran')->sum('jumlah'),
                ];
            })
            ->values();

        $saldoBerjalan = $saldoAwalPeriode;
        $harian = $rows->map(function ($row) use (&$saldoBerjalan) {
            $saldoBerjalan += $row['pemasukan'] - $row['pengeluaran'];
            $row['saldo'] = $saldoBerjalan;

            return $row;
        });

        $totalPemasukan = $harian->sum('pemasukan');
        $totalPengeluaran = $harian->sum('pengeluaran');
        $saldoAkhirPeriode = $saldoAwalPeriode + $totalPemasukan - $totalPengeluaran;

        return view('reports.arus-kas', compact(
            'dari', 'sampai', 'harian',
            'saldoAwalPeriode', 'saldoAkhirPeriode',
            'totalPemasukan', 'totalPengeluaran'
        ));
    }

    /**
     * NERACA (POSISI KEUANGAN)
     * Aset = Kewajiban + Modal, per tanggal tertentu (default: hari ini).
     * Modal akhir = Modal awal (input manual) + akumulasi laba/rugi sejak awal pencatatan s.d. tanggal neraca.
     */
    public function neraca(Request $request)
    {
        $perTanggal = $request->input('per_tanggal', now()->toDateString());

        $akunKas = Account::where('type', 'kas')->get();
        $akunAsetTetap = Account::where('type', 'aset_tetap')->get();
        $akunKewajiban = Account::where('type', 'kewajiban')->get();
        $akunModal = Account::where('type', 'modal')->get();

        $totalKas = $akunKas->sum(fn ($a) => $a->saldoBerjalan($perTanggal));
        $totalAsetTetap = $akunAsetTetap->sum('saldo_awal');
        $totalAset = $totalKas + $totalAsetTetap;

        $totalKewajiban = $akunKewajiban->sum('saldo_awal');

        $totalPemasukanKumulatif = Transaction::where('jenis', 'pemasukan')
            ->whereDate('tanggal', '<=', $perTanggal)
            ->sum('jumlah');
        $totalPengeluaranKumulatif = Transaction::where('jenis', 'pengeluaran')
            ->whereDate('tanggal', '<=', $perTanggal)
            ->sum('jumlah');
        $labaDitahan = $totalPemasukanKumulatif - $totalPengeluaranKumulatif;

        $totalModalAwal = $akunModal->sum('saldo_awal');
        $totalModal = $totalModalAwal + $labaDitahan;

        $totalKewajibanDanModal = $totalKewajiban + $totalModal;
        $selisih = round($totalAset - $totalKewajibanDanModal, 2);

        return view('reports.neraca', compact(
            'perTanggal',
            'akunKas', 'akunAsetTetap', 'akunKewajiban', 'akunModal',
            'totalKas', 'totalAsetTetap', 'totalAset',
            'totalKewajiban', 'totalModalAwal', 'labaDitahan', 'totalModal',
            'totalKewajibanDanModal', 'selisih'
        ));
    }

    public function exportLabaRugi(Request $request)
    {
        $dari = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->toDateString());

        $pemasukanPerKategori = Transaction::with('category')
            ->where('jenis', 'pemasukan')->periode($dari, $sampai)->get()
            ->groupBy('category.name')->map(fn ($rows) => $rows->sum('jumlah'));

        $pengeluaranPerKategori = Transaction::with('category')
            ->where('jenis', 'pengeluaran')->periode($dari, $sampai)->get()
            ->groupBy('category.name')->map(fn ($rows) => $rows->sum('jumlah'));

        $filename = 'laba-rugi-'.$dari.'_sd_'.$sampai.'.csv';

        return response()->streamDownload(function () use ($pemasukanPerKategori, $pengeluaranPerKategori, $dari, $sampai) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Laporan Laba Rugi', $dari.' s.d. '.$sampai]);
            fputcsv($handle, []);
            fputcsv($handle, ['PEMASUKAN']);
            fputcsv($handle, ['Kategori', 'Jumlah (Rp)']);
            foreach ($pemasukanPerKategori as $nama => $jumlah) {
                fputcsv($handle, [$nama, number_format($jumlah, 2, '.', '')]);
            }
            fputcsv($handle, ['Total Pemasukan', number_format($pemasukanPerKategori->sum(), 2, '.', '')]);
            fputcsv($handle, []);
            fputcsv($handle, ['PENGELUARAN']);
            fputcsv($handle, ['Kategori', 'Jumlah (Rp)']);
            foreach ($pengeluaranPerKategori as $nama => $jumlah) {
                fputcsv($handle, [$nama, number_format($jumlah, 2, '.', '')]);
            }
            fputcsv($handle, ['Total Pengeluaran', number_format($pengeluaranPerKategori->sum(), 2, '.', '')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Laba/Rugi Bersih', number_format($pemasukanPerKategori->sum() - $pengeluaranPerKategori->sum(), 2, '.', '')]);
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportArusKas(Request $request)
    {
        $dari = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->toDateString());

        $akunKas = Account::where('type', 'kas')->get();
        $saldoAwalPeriode = $akunKas->sum(fn ($a) => $a->saldoBerjalan(
            \Illuminate\Support\Carbon::parse($dari)->subDay()->toDateString()
        ));

        $harian = Transaction::whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal')->get()
            ->groupBy(fn ($t) => $t->tanggal->toDateString())
            ->map(fn ($items, $tanggal) => [
                'tanggal' => $tanggal,
                'pemasukan' => $items->where('jenis', 'pemasukan')->sum('jumlah'),
                'pengeluaran' => $items->where('jenis', 'pengeluaran')->sum('jumlah'),
            ])->values();

        $saldoBerjalan = $saldoAwalPeriode;
        $harian = $harian->map(function ($row) use (&$saldoBerjalan) {
            $saldoBerjalan += $row['pemasukan'] - $row['pengeluaran'];
            $row['saldo'] = $saldoBerjalan;

            return $row;
        });

        $filename = 'arus-kas-'.$dari.'_sd_'.$sampai.'.csv';

        return response()->streamDownload(function () use ($harian, $saldoAwalPeriode) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Saldo Awal Periode', number_format($saldoAwalPeriode, 2, '.', '')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Tanggal', 'Kas Masuk', 'Kas Keluar', 'Saldo']);
            foreach ($harian as $row) {
                fputcsv($handle, [
                    $row['tanggal'],
                    number_format($row['pemasukan'], 2, '.', ''),
                    number_format($row['pengeluaran'], 2, '.', ''),
                    number_format($row['saldo'], 2, '.', ''),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportNeraca(Request $request)
    {
        $perTanggal = $request->input('per_tanggal', now()->toDateString());

        $akunKas = Account::where('type', 'kas')->get();
        $akunAsetTetap = Account::where('type', 'aset_tetap')->get();
        $akunKewajiban = Account::where('type', 'kewajiban')->get();
        $akunModal = Account::where('type', 'modal')->get();

        $totalKas = $akunKas->sum(fn ($a) => $a->saldoBerjalan($perTanggal));
        $totalAsetTetap = $akunAsetTetap->sum('saldo_awal');
        $totalKewajiban = $akunKewajiban->sum('saldo_awal');

        $labaDitahan = Transaction::where('jenis', 'pemasukan')->whereDate('tanggal', '<=', $perTanggal)->sum('jumlah')
            - Transaction::where('jenis', 'pengeluaran')->whereDate('tanggal', '<=', $perTanggal)->sum('jumlah');
        $totalModal = $akunModal->sum('saldo_awal') + $labaDitahan;

        $filename = 'neraca-'.$perTanggal.'.csv';

        return response()->streamDownload(function () use ($akunKas, $akunAsetTetap, $akunKewajiban, $akunModal, $totalKas, $totalAsetTetap, $totalKewajiban, $totalModal, $labaDitahan, $perTanggal) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Neraca per', $perTanggal]);
            fputcsv($handle, []);
            fputcsv($handle, ['ASET']);
            foreach ($akunKas as $a) {
                fputcsv($handle, [$a->name, number_format($a->saldoBerjalan($perTanggal), 2, '.', '')]);
            }
            foreach ($akunAsetTetap as $a) {
                fputcsv($handle, [$a->name, number_format($a->saldo_awal, 2, '.', '')]);
            }
            fputcsv($handle, ['Total Aset', number_format($totalKas + $totalAsetTetap, 2, '.', '')]);
            fputcsv($handle, []);
            fputcsv($handle, ['KEWAJIBAN']);
            foreach ($akunKewajiban as $a) {
                fputcsv($handle, [$a->name, number_format($a->saldo_awal, 2, '.', '')]);
            }
            fputcsv($handle, ['Total Kewajiban', number_format($totalKewajiban, 2, '.', '')]);
            fputcsv($handle, []);
            fputcsv($handle, ['MODAL']);
            foreach ($akunModal as $a) {
                fputcsv($handle, [$a->name, number_format($a->saldo_awal, 2, '.', '')]);
            }
            fputcsv($handle, ['Laba Ditahan', number_format($labaDitahan, 2, '.', '')]);
            fputcsv($handle, ['Total Modal', number_format($totalModal, 2, '.', '')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Total Kewajiban + Modal', number_format($totalKewajiban + $totalModal, 2, '.', '')]);
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
