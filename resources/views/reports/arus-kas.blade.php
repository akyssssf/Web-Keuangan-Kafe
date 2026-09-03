@extends('layouts.app')

@section('title', 'Laporan Arus Kas')
@section('desc', 'Pergerakan kas masuk & keluar harian pada periode yang dipilih')

@section('content')

<div class="card" style="margin-bottom:18px;">
    <form method="GET" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
        <div>
            <label>Dari Tanggal</label>
            <input type="date" name="dari" value="{{ $dari }}">
        </div>
        <div>
            <label>Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ $sampai }}">
        </div>
        <button class="btn btn-primary" type="submit">Tampilkan</button>
        <a href="{{ route('reports.arus-kas.export', ['dari' => $dari, 'sampai' => $sampai]) }}" class="btn btn-ghost">⬇ Ekspor CSV</a>
    </form>
</div>

<div class="grid-2" style="grid-template-columns: repeat(4, 1fr); margin-bottom:18px;">
    <div class="card">
        <div class="stat-label">Saldo Awal Periode</div>
        <div class="stat-value">Rp {{ number_format($saldoAwalPeriode, 0, ',', '.') }}</div>
    </div>
    <div class="card">
        <div class="stat-label">Total Kas Masuk</div>
        <div class="stat-value" style="color:var(--pine)">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
    </div>
    <div class="card">
        <div class="stat-label">Total Kas Keluar</div>
        <div class="stat-value" style="color:var(--brick)">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
    </div>
    <div class="card">
        <div class="stat-label">Saldo Akhir Periode</div>
        <div class="stat-value">Rp {{ number_format($saldoAkhirPeriode, 0, ',', '.') }}</div>
    </div>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th style="text-align:right;">Kas Masuk</th>
                <th style="text-align:right;">Kas Keluar</th>
                <th style="text-align:right;">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($harian as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('d M Y') }}</td>
                    <td class="num" style="text-align:right; color:var(--pine);">{{ $row['pemasukan'] > 0 ? '+Rp '.number_format($row['pemasukan'],0,',','.') : '—' }}</td>
                    <td class="num" style="text-align:right; color:var(--brick);">{{ $row['pengeluaran'] > 0 ? '-Rp '.number_format($row['pengeluaran'],0,',','.') : '—' }}</td>
                    <td class="num" style="text-align:right; font-weight:600;">Rp {{ number_format($row['saldo'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center; color:var(--ink-soft); padding:30px;">Tidak ada transaksi pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
