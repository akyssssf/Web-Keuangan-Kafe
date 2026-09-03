@extends('layouts.app')

@section('title', 'Neraca')
@section('desc', 'Posisi keuangan (Aset = Kewajiban + Modal) per tanggal tertentu')

@section('content')

<div class="card" style="margin-bottom:18px;">
    <form method="GET" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
        <div>
            <label>Per Tanggal</label>
            <input type="date" name="per_tanggal" value="{{ $perTanggal }}">
        </div>
        <button class="btn btn-primary" type="submit">Tampilkan</button>
        <a href="{{ route('reports.neraca.export', ['per_tanggal' => $perTanggal]) }}" class="btn btn-ghost">⬇ Ekspor CSV</a>
    </form>
</div>

<div class="grid-2" style="align-items:start;">
    <div class="card">
        <div style="font-weight:600; margin-bottom:14px;">Aset</div>

        <div style="font-size:12.5px; color:var(--ink-soft); margin-bottom:6px;">Aset Lancar (Kas &amp; Bank)</div>
        <table>
            <tbody>
                @foreach ($akunKas as $a)
                    <tr>
                        <td>{{ $a->name }}</td>
                        <td class="num" style="text-align:right;">Rp {{ number_format($a->saldoBerjalan($perTanggal), 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="font-size:12.5px; color:var(--ink-soft); margin:14px 0 6px;">Aset Tetap</div>
        <table>
            <tbody>
                @foreach ($akunAsetTetap as $a)
                    <tr>
                        <td>{{ $a->name }}</td>
                        <td class="num" style="text-align:right;">Rp {{ number_format($a->saldo_awal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <hr class="divider-line">
        <div style="display:flex; justify-content:space-between; font-weight:700;">
            <span>Total Aset</span>
            <span class="num">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="card">
        <div style="font-weight:600; margin-bottom:14px;">Kewajiban &amp; Modal</div>

        <div style="font-size:12.5px; color:var(--ink-soft); margin-bottom:6px;">Kewajiban</div>
        <table>
            <tbody>
                @forelse ($akunKewajiban as $a)
                    <tr>
                        <td>{{ $a->name }}</td>
                        <td class="num" style="text-align:right;">Rp {{ number_format($a->saldo_awal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td style="color:var(--ink-soft);">Tidak ada kewajiban tercatat.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="display:flex; justify-content:space-between; font-weight:600; margin-top:8px;">
            <span>Total Kewajiban</span>
            <span class="num">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</span>
        </div>

        <div style="font-size:12.5px; color:var(--ink-soft); margin:18px 0 6px;">Modal</div>
        <table>
            <tbody>
                <tr>
                    <td>Modal Awal (input manual)</td>
                    <td class="num" style="text-align:right;">Rp {{ number_format($totalModalAwal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Laba Ditahan (akumulasi laba/rugi s.d. tanggal ini)</td>
                    <td class="num" style="text-align:right; color: {{ $labaDitahan >= 0 ? 'var(--pine)' : 'var(--brick)' }}">Rp {{ number_format($labaDitahan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        <div style="display:flex; justify-content:space-between; font-weight:600; margin-top:8px;">
            <span>Total Modal</span>
            <span class="num">Rp {{ number_format($totalModal, 0, ',', '.') }}</span>
        </div>

        <hr class="divider-line">
        <div style="display:flex; justify-content:space-between; font-weight:700;">
            <span>Total Kewajiban + Modal</span>
            <span class="num">Rp {{ number_format($totalKewajibanDanModal, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

<div class="card" style="margin-top:18px; background: {{ abs($selisih) < 1 ? 'var(--pine-soft)' : 'var(--brick-soft)' }}; border:none;">
    @if (abs($selisih) < 1)
        <strong style="color:var(--pine);">Neraca seimbang.</strong>
        <span style="color:var(--ink-soft);">Total Aset sama dengan Total Kewajiban + Modal, per {{ \Carbon\Carbon::parse($perTanggal)->translatedFormat('d F Y') }}.</span>
    @else
        <strong style="color:var(--brick);">Neraca belum seimbang, selisih Rp {{ number_format(abs($selisih), 0, ',', '.') }}.</strong>
        <span style="color:var(--ink-soft);">Cek kembali saldo awal pos aset tetap, kewajiban, atau modal.</span>
    @endif
</div>

@endsection
