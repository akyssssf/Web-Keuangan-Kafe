@extends('layouts.app')

@section('title', 'Kategori')
@section('desc', 'Kelola kategori pemasukan & pengeluaran untuk penyusunan Laba Rugi')

@section('content')

<div class="grid-2" style="align-items:start;">
    <div class="card">
        <div style="font-weight:600; margin-bottom:14px;">Tambah Kategori</div>
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="field">
                <label>Nama Kategori</label>
                <input type="text" name="name" placeholder="Contoh: Penjualan Kopi Susu" required>
            </div>
            <div class="field">
                <label>Jenis</label>
                <select name="type" required>
                    <option value="pemasukan">Pemasukan</option>
                    <option value="pengeluaran">Pengeluaran</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Tambah</button>
        </form>
    </div>

    <div class="card">
        <div style="font-weight:600; margin-bottom:14px;">Daftar Kategori</div>
        <table>
            <tbody>
                @foreach ($kategori as $k)
                    <tr>
                        <td>
                            <span class="tag {{ $k->type === 'pemasukan' ? 'tag-in' : 'tag-out' }}">{{ $k->type === 'pemasukan' ? 'Masuk' : 'Keluar' }}</span>
                            {{ $k->name }}
                        </td>
                        <td style="text-align:right;">
                            <form action="{{ route('categories.destroy', $k) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger-text">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
