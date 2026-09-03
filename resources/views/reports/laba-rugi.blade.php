@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')
@section('desc', 'Perbandingan pemasukan dan pengeluaran pada periode yang dipilih')

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
        <a href="{{ route('reports.laba-rugi.export', ['dari' => $dari, 'sampai' => $sampai]) }}" class="btn btn-ghost">⬇ Ekspor CSV</a>
    </form>
</div>

<div class="grid-2" style="align-items:start;">
    <div class="card">
        <div style="font-weight:600; color:var(--pine); margin-bottom:14px;">Pemasukan</div>
        <table>
            <tbody>
                @forelse ($pemasukanPerKategori as $nama => $jumlah)
                    <tr>
                        <td>{{ $nama }}</td>
                        <td class="num" style="text-align:right;">Rp {{ number_format($jumlah, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td style="color:var(--ink-soft);">Tidak ada data pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
        <hr class="divider-line">
        <div style="display:flex; justify-content:space-between; font-weight:700;">
            <span>Total Pemasukan</span>
            <span class="num">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="card">
        <div style="font-weight:600; color:var(--brick); margin-bottom:14px;">Pengeluaran</div>
        <table>
            <tbody>
                @forelse ($pengeluaranPerKategori as $nama => $jumlah)
                    <tr>
                        <td>{{ $nama }}</td>
                        <td class="num" style="text-align:right;">Rp {{ number_format($jumlah, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td style="color:var(--ink-soft);">Tidak ada data pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
        <hr class="divider-line">
        <div style="display:flex; justify-content:space-between; font-weight:700;">
            <span>Total Pengeluaran</span>
            <span class="num">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

<div class="card" style="margin-top:18px; background: {{ $labaRugiBersih >= 0 ? 'var(--pine-soft)' : 'var(--brick-soft)' }}; border:none;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <div style="font-weight:600;">{{ $labaRugiBersih >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}</div>
            <div style="font-size:12.5px; color:var(--ink-soft);">Periode {{ \Carbon\Carbon::parse($dari)->translatedFormat('d M Y') }} — {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') }}</div>
        </div>
        <div class="num" style="font-size:26px; font-weight:700; color: {{ $labaRugiBersih >= 0 ? 'var(--pine)' : 'var(--brick)' }}">
            Rp {{ number_format(abs($labaRugiBersih), 0, ',', '.') }}
        </div>
    </div>
</div>

@endsection
