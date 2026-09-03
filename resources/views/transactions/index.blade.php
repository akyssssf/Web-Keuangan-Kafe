@extends('layouts.app')

@section('title', 'Catat Transaksi')
@section('desc', 'Semua pemasukan dan pengeluaran kafe tercatat di sini')

@section('actions')
    <a href="{{ route('transactions.export', request()->query()) }}" class="btn btn-ghost">⬇ Ekspor CSV</a>
    <a href="{{ route('transactions.create') }}" class="btn btn-primary">+ Catat Transaksi</a>
@endsection

@section('content')

<div class="card" style="margin-bottom:18px;">
    <form method="GET" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
        <div style="min-width:150px;">
            <label>Jenis</label>
            <select name="jenis">
                <option value="">Semua</option>
                <option value="pemasukan" @selected(request('jenis')==='pemasukan')>Pemasukan</option>
                <option value="pengeluaran" @selected(request('jenis')==='pengeluaran')>Pengeluaran</option>
            </select>
        </div>
        <div>
            <label>Dari Tanggal</label>
            <input type="date" name="dari" value="{{ request('dari') }}">
        </div>
        <div>
            <label>Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ request('sampai') }}">
        </div>
        <button class="btn btn-ghost" type="submit">Terapkan Filter</button>
        <a href="{{ route('transactions.index') }}" class="btn btn-ghost">Reset</a>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Pos Kas</th>
                <th>Keterangan</th>
                <th style="text-align:right;">Jumlah</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksi as $t)
                <tr>
                    <td>{{ $t->tanggal->translatedFormat('d M Y') }}</td>
                    <td>
                        <span class="tag {{ $t->jenis === 'pemasukan' ? 'tag-in' : 'tag-out' }}">{{ $t->category->name }}</span>
                    </td>
                    <td>{{ $t->account->name }}</td>
                    <td style="color:var(--ink-soft);">{{ $t->keterangan ?: '—' }}</td>
                    <td class="num" style="text-align:right; font-weight:600; color: {{ $t->jenis === 'pemasukan' ? 'var(--pine)' : 'var(--brick)' }}">
                        {{ $t->jenis === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                    </td>
                    <td style="text-align:right; white-space:nowrap;">
                        <a href="{{ route('transactions.edit', $t) }}" style="font-size:13px; margin-right:10px;">Ubah</a>
                        <form action="{{ route('transactions.destroy', $t) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus transaksi ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger-text">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; color:var(--ink-soft); padding:30px;">Belum ada transaksi yang cocok dengan filter.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px;">{{ $transaksi->links() }}</div>

@endsection
