@extends('layouts.app')

@section('title', 'Ubah Transaksi')
@section('desc', 'Perbarui detail transaksi yang sudah dicatat')

@section('content')

<div class="card" style="max-width:560px;">
    <form method="POST" action="{{ route('transactions.update', $transaction) }}">
        @csrf @method('PUT')

        <div class="field">
            <label>Jenis Transaksi</label>
            <select name="jenis" id="jenis" required onchange="toggleKategori()">
                <option value="pemasukan" {{ $transaction->jenis === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                <option value="pengeluaran" {{ $transaction->jenis === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
            </select>
        </div>

        <div class="grid-2">
            <div class="field">
                <label>Tanggal</label>
                <input type="date" name="tanggal" value="{{ $transaction->tanggal->toDateString() }}" required>
            </div>
            <div class="field">
                <label>Jumlah (Rp)</label>
                <input type="number" step="0.01" min="0.01" name="jumlah" value="{{ $transaction->jumlah }}" required>
            </div>
        </div>

        <div class="field" id="kategori-pemasukan">
            <label>Kategori Pemasukan</label>
            <select onchange="document.getElementById('category_id').value=this.value">
                @foreach ($kategoriPemasukan as $k)
                    <option value="{{ $k->id }}" {{ $transaction->category_id == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="field" id="kategori-pengeluaran" style="display:none;">
            <label>Kategori Pengeluaran</label>
            <select onchange="document.getElementById('category_id').value=this.value">
                @foreach ($kategoriPengeluaran as $k)
                    <option value="{{ $k->id }}" {{ $transaction->category_id == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="category_id" id="category_id" value="{{ $transaction->category_id }}">

        <div class="field">
            <label>Pos Kas</label>
            <select name="account_id" required>
                @foreach ($akunKas as $a)
                    <option value="{{ $a->id }}" {{ $transaction->account_id == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label>Keterangan (opsional)</label>
            <textarea name="keterangan" rows="2">{{ $transaction->keterangan }}</textarea>
        </div>

        @if ($errors->any())
            <div class="flash flash-err">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display:flex; gap:10px; margin-top:8px;">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('transactions.index') }}" class="btn btn-ghost">Batal</a>
        </div>
    </form>
</div>

<script>
    function toggleKategori() {
        const jenis = document.getElementById('jenis').value;
        document.getElementById('kategori-pemasukan').style.display = jenis === 'pemasukan' ? 'block' : 'none';
        document.getElementById('kategori-pengeluaran').style.display = jenis === 'pengeluaran' ? 'block' : 'none';
    }
    toggleKategori();
</script>

@endsection
