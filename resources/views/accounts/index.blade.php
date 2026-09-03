@extends('layouts.app')

@section('title', 'Pos Kas & Aset')
@section('desc', 'Data induk pos kas, aset tetap, kewajiban, dan modal — dasar penyusunan Neraca')

@section('content')

<div class="grid-2" style="align-items:start; grid-template-columns: 340px 1fr;">
    <div class="card">
        <div style="font-weight:600; margin-bottom:14px;">Tambah Pos Baru</div>
        <form method="POST" action="{{ route('accounts.store') }}">
            @csrf
            <div class="field">
                <label>Nama Pos</label>
                <input type="text" name="name" placeholder="Contoh: Kas Tunai Kasir" required>
            </div>
            <div class="field">
                <label>Jenis</label>
                <select name="type" required>
                    <option value="kas">Kas / Bank (otomatis dari transaksi)</option>
                    <option value="aset_tetap">Aset Tetap</option>
                    <option value="kewajiban">Kewajiban (Hutang)</option>
                    <option value="modal">Modal</option>
                </select>
            </div>
            <div class="field">
                <label>Saldo Awal (Rp)</label>
                <input type="number" step="0.01" name="saldo_awal" value="0" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah</button>
        </form>
    </div>

    <div class="card">
        <div style="font-weight:600; margin-bottom:14px;">Daftar Pos</div>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Jenis</th>
                    <th style="text-align:right;">Saldo Awal</th>
                    <th style="text-align:right;">Saldo Berjalan</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($akun as $a)
                    <tr>
                        <td>{{ $a->name }}</td>
                        <td style="color:var(--ink-soft);">
                            @php
                                $labelJenis = [
                                    'kas' => 'Kas / Bank',
                                    'aset_tetap' => 'Aset Tetap',
                                    'kewajiban' => 'Kewajiban',
                                    'modal' => 'Modal',
                                ];
                            @endphp
                            {{ $labelJenis[$a->type] }}
                        </td>
                        <td class="num" style="text-align:right;">Rp {{ number_format($a->saldo_awal, 0, ',', '.') }}</td>
                        <td class="num" style="text-align:right; font-weight:600;">
                            @if ($a->type === 'kas')
                                Rp {{ number_format($a->saldoBerjalan(), 0, ',', '.') }}
                            @else
                                <span style="color:var(--ink-soft); font-weight:400;">—</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <form action="{{ route('accounts.destroy', $a) }}" method="POST" onsubmit="return confirm('Hapus pos ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger-text">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p style="font-size:12.5px; color:var(--ink-soft); margin-top:14px;">
            Saldo berjalan hanya dihitung otomatis untuk pos jenis "Kas / Bank" berdasarkan transaksi yang tercatat. Untuk aset tetap, kewajiban, dan modal, perbarui saldonya secara berkala sesuai kondisi sebenarnya.
        </p>
    </div>
</div>

@endsection
