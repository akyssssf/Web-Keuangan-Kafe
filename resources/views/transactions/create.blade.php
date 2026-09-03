@extends('layouts.app')

@section('title', 'Catat Transaksi Baru')
@section('desc', 'Isi detail pemasukan atau pengeluaran kafe')

@section('content')

<div class="card" style="max-width:560px;">
    <form method="POST" action="{{ route('transactions.store') }}">
        @csrf

        <div class="field">
            <label>Jenis Transaksi</label>
            <select name="jenis" id="jenis" required onchange="toggleKategori()">
                <option value="pemasukan" {{ old('jenis') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                <option value="pengeluaran" {{ old('jenis') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
            </select>
        </div>

        <div class="grid-2">
            <div class="field">
                <label>Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required>
            </div>
            <div class="field">
                <label>Jumlah (Rp)</label>
                <input type="number" step="0.01" min="0.01" name="jumlah" value="{{ old('jumlah') }}" placeholder="150000" required>
            </div>
        </div>

        <div class="field" id="kategori-pemasukan">
            <label>Kategori Pemasukan</label>
            <select name="category_id_pemasukan" onchange="document.getElementById('category_id').value=this.value">
                @foreach ($kategoriPemasukan as $k)
                    <option value="{{ $k->id }}">{{ $k->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="field" id="kategori-pengeluaran" style="display:none;">
            <label>Kategori Pengeluaran</label>
            <select name="category_id_pengeluaran" onchange="document.getElementById('category_id').value=this.value">
                @foreach ($kategoriPengeluaran as $k)
                    <option value="{{ $k->id }}">{{ $k->name }}</option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="category_id" id="category_id" value="{{ $kategoriPemasukan->first()->id ?? '' }}">

        <div class="field">
            <label>Pos Kas (uang masuk/keluar lewat mana)</label>
            <select name="account_id" required>
                @foreach ($akunKas as $a)
                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label>Keterangan (opsional)</label>
            <textarea name="keterangan" rows="2" placeholder="Contoh: Beli biji kopi Arabika 5kg">{{ old('keterangan') }}</textarea>
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
            <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
            <a href="{{ route('transactions.index') }}" class="btn btn-ghost">Batal</a>
        </div>
    </form>
</div>

<script>
    function toggleKategori() {
        const jenis = document.getElementById('jenis').value;
        const pemasukanDiv = document.getElementById('kategori-pemasukan');
        const pengeluaranDiv = document.getElementById('kategori-pengeluaran');
        const categoryInput = document.getElementById('category_id');

        if (jenis === 'pemasukan') {
            pemasukanDiv.style.display = 'block';
            pengeluaranDiv.style.display = 'none';
            categoryInput.value = pemasukanDiv.querySelector('select').value;
        } else {
            pemasukanDiv.style.display = 'none';
            pengeluaranDiv.style.display = 'block';
            categoryInput.value = pengeluaranDiv.querySelector('select').value;
        }
    }
    toggleKategori();
</script>

@endsection
