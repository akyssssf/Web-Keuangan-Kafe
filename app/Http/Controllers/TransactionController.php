<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['category', 'account']);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        $transaksi = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('transactions.index', compact('transaksi'));
    }

    public function create()
    {
        $kategoriPemasukan = Category::pemasukan()->orderBy('name')->get();
        $kategoriPengeluaran = Category::pengeluaran()->orderBy('name')->get();
        $akunKas = Account::where('type', 'kas')->orderBy('name')->get();

        return view('transactions.create', compact('kategoriPemasukan', 'kategoriPengeluaran', 'akunKas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', 'in:pemasukan,pengeluaran'],
            'category_id' => ['required', 'exists:categories,id'],
            'account_id' => ['required', 'exists:accounts,id'],
            'jumlah' => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        Transaction::create($data);

        return redirect()->route('transactions.index')->with('status', 'Transaksi berhasil dicatat.');
    }

    public function edit(Transaction $transaction)
    {
        $kategoriPemasukan = Category::pemasukan()->orderBy('name')->get();
        $kategoriPengeluaran = Category::pengeluaran()->orderBy('name')->get();
        $akunKas = Account::where('type', 'kas')->orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'kategoriPemasukan', 'kategoriPengeluaran', 'akunKas'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', 'in:pemasukan,pengeluaran'],
            'category_id' => ['required', 'exists:categories,id'],
            'account_id' => ['required', 'exists:accounts,id'],
            'jumlah' => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $transaction->update($data);

        return redirect()->route('transactions.index')->with('status', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('transactions.index')->with('status', 'Transaksi berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $query = Transaction::with(['category', 'account']);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        $transaksi = $query->orderBy('tanggal')->orderBy('id')->get();

        $filename = 'transaksi-'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($transaksi) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // BOM supaya karakter khusus tampil benar di Excel
            fputcsv($handle, ['Tanggal', 'Jenis', 'Kategori', 'Pos Kas', 'Keterangan', 'Jumlah (Rp)']);

            foreach ($transaksi as $t) {
                fputcsv($handle, [
                    $t->tanggal->format('Y-m-d'),
                    ucfirst($t->jenis),
                    $t->category->name,
                    $t->account->name,
                    $t->keterangan,
                    number_format($t->jumlah, 2, '.', ''),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
