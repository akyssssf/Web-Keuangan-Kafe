<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $akun = Account::orderBy('type')->orderBy('name')->get();

        return view('accounts.index', compact('akun'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:kas,aset_tetap,kewajiban,modal'],
            'saldo_awal' => ['required', 'numeric'],
        ]);

        Account::create($data);

        return back()->with('status', 'Pos akun berhasil ditambahkan.');
    }

    public function update(Request $request, Account $account)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'saldo_awal' => ['required', 'numeric'],
        ]);

        $account->update($data);

        return back()->with('status', 'Pos akun berhasil diperbarui.');
    }

    public function destroy(Account $account)
    {
        if ($account->transactions()->exists()) {
            return back()->with('error', 'Pos akun tidak bisa dihapus karena masih dipakai di transaksi.');
        }

        $account->delete();

        return back()->with('status', 'Pos akun berhasil dihapus.');
    }
}
