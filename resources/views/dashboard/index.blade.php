@extends('layouts.app')

@section('title', 'Ringkasan')
@section('desc', 'Kondisi keuangan kafe per hari ini, ' . now()->translatedFormat('l, d F Y'))

@section('actions')
    <a href="{{ route('transactions.create') }}" class="btn btn-primary">+ Catat Transaksi</a>
@endsection

@section('content')

<div class="grid-2" style="grid-template-columns: repeat(4, 1fr); margin-bottom:22px;">
    <div class="card">
        <div class="stat-label">Kas Tersedia</div>
        <div class="stat-value">Rp {{ number_format($totalKas, 0, ',', '.') }}</div>
    </div>
    <div class="card">
        <div class="stat-label">Pemasukan Bulan Ini</div>
        <div class="stat-value" style="color: var(--pine)">Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</div>
    </div>
    <div class="card">
        <div class="stat-label">Pengeluaran Bulan Ini</div>
        <div class="stat-value" style="color: var(--brick)">Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</div>
    </div>
    <div class="card">
        <div class="stat-label">Laba Bulan Ini</div>
        <div class="stat-value" style="color: {{ $labaBulanIni >= 0 ? 'var(--pine)' : 'var(--brick)' }}">Rp {{ number_format($labaBulanIni, 0, ',', '.') }}</div>
    </div>
</div>

<div class="grid-2" style="align-items:start;">
    <div class="card">
        <div style="font-weight:600; margin-bottom:14px;">Arus 7 Hari Terakhir</div>
        <div style="display:flex; align-items:flex-end; gap:10px; height:160px;">
            @php $maxVal = max(1, $grafik->max(fn($g) => max($g['pemasukan'], $g['pengeluaran']))); @endphp
            @foreach ($grafik as $g)
                <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:4px;">
                    <div style="width:100%; display:flex; gap:3px; align-items:flex-end; height:120px;">
                        <div style="flex:1; background: var(--pine); border-radius: 3px 3px 0 0; height: {{ max(3, ($g['pemasukan']/$maxVal)*120) }}px;" title="Pemasukan: Rp {{ number_format($g['pemasukan'],0,',','.') }}"></div>
                        <div style="flex:1; background: var(--brick); opacity:0.75; border-radius: 3px 3px 0 0; height: {{ max(3, ($g['pengeluaran']/$maxVal)*120) }}px;" title="Pengeluaran: Rp {{ number_format($g['pengeluaran'],0,',','.') }}"></div>
                    </div>
                    <div style="font-size:11px; color:var(--ink-soft);">{{ $g['label'] }}</div>
                </div>
            @endforeach
        </div>
        <div style="display:flex; gap:16px; margin-top:14px; font-size:12px; color:var(--ink-soft);">
            <span><span style="display:inline-block;width:9px;height:9px;background:var(--pine);border-radius:2px;margin-right:5px;"></span>Pemasukan</span>
            <span><span style="display:inline-block;width:9px;height:9px;background:var(--brick);opacity:0.75;border-radius:2px;margin-right:5px;"></span>Pengeluaran</span>
        </div>
    </div>

    <div class="card">
        <div style="font-weight:600; margin-bottom:14px;">Transaksi Terbaru</div>
        @forelse ($transaksiTerbaru as $t)
            <div style="display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-bottom:1px solid #F0EBE0;">
                <div>
                    <div style="font-size:14px;">{{ $t->category->name }}</div>
                    <div style="font-size:12px; color:var(--ink-soft);">{{ $t->tanggal->translatedFormat('d M Y') }} · {{ $t->account->name }}</div>
                </div>
                <div class="num" style="font-weight:600; color: {{ $t->jenis === 'pemasukan' ? 'var(--pine)' : 'var(--brick)' }}">
                    {{ $t->jenis === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                </div>
            </div>
        @empty
            <p style="color:var(--ink-soft); font-size:14px;">Belum ada transaksi. Mulai catat transaksi pertama kafe kamu.</p>
        @endforelse
    </div>
</div>

@endsection
