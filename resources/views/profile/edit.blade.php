@extends('layouts.app')

@section('title', 'Profil Saya')
@section('desc', 'Ubah nama, email, dan kata sandi akun kamu')

@section('content')

<div class="grid-2" style="max-width:900px; align-items:start;">
    <div class="card">
        <div style="font-weight:700; margin-bottom:4px;">Informasi Akun</div>
        <p style="font-size:13px; color:var(--ink-soft); margin:0 0 18px;">Nama dan email dipakai untuk login.</p>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf @method('PUT')

            <div class="field">
                <label>Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>

            <hr class="divider-line">

            <div style="font-weight:700; margin-bottom:4px;">Ubah Kata Sandi</div>
            <p style="font-size:13px; color:var(--ink-soft); margin:0 0 16px;">Kosongkan bagian ini kalau tidak ingin mengubah kata sandi.</p>

            <div class="field">
                <label>Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" placeholder="Wajib diisi kalau mau ganti kata sandi">
            </div>

            <div class="grid-2">
                <div class="field">
                    <label>Kata Sandi Baru</label>
                    <input type="password" name="new_password" placeholder="Minimal 8 karakter">
                </div>
                <div class="field">
                    <label>Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="new_password_confirmation">
                </div>
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

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>

    <div class="card" style="background:var(--page-bg); border-style:dashed;">
        <div style="font-weight:700; margin-bottom:8px;">Tips Keamanan</div>
        <ul style="font-size:13.5px; color:var(--ink-soft); padding-left:18px; line-height:1.8; margin:0;">
            <li>Gunakan kata sandi yang tidak dipakai di akun lain.</li>
            <li>Segera ganti kata sandi bawaan (<code>kafe12345</code>) kalau belum pernah diganti.</li>
            <li>Jangan bagikan email & kata sandi login ke orang yang tidak berkepentingan.</li>
        </ul>
    </div>
</div>

@endsection
