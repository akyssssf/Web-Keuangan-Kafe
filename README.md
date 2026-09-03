# Buku Kas Kafe

Aplikasi pencatatan keuangan kafe berbasis Laravel 12. Mencatat transaksi harian (pemasukan & pengeluaran), lalu otomatis mengolahnya menjadi 3 laporan keuangan utama (SPS):

1. **Laba Rugi** — total pemasukan vs pengeluaran per kategori, pada periode tertentu.
2. **Arus Kas** — pergerakan kas masuk/keluar harian, saldo awal & akhir periode.
3. **Neraca** — posisi keuangan (Aset = Kewajiban + Modal) per tanggal tertentu.

## Fitur

- Dashboard ringkasan: kas tersedia, laba bulan berjalan, grafik 7 hari terakhir, transaksi terbaru.
- Catat transaksi (tambah/ubah/hapus), dengan filter jenis & rentang tanggal.
- Kelola kategori pemasukan/pengeluaran sendiri (fleksibel, tidak hardcode).
- Kelola pos kas, aset tetap, kewajiban, dan modal — dasar penyusunan Neraca.
- Semua laporan bisa difilter berdasarkan periode/tanggal.

## Kebutuhan Sistem

- PHP 8.2 atau lebih baru
- Composer (https://getcomposer.org)
- Ekstensi PHP: `sqlite3` / `pdo_sqlite` (default sudah dipakai supaya tidak perlu install database server)

## Cara Instalasi (dari Nol)

Buka terminal di folder project ini, lalu jalankan berurutan:

```bash
# 1. Install dependency Laravel
composer install

# 2. Salin file environment
cp .env.example .env

# 3. Generate application key
php artisan key:generate

# 4. Siapkan file database SQLite (kalau belum ada)
touch database/database.sqlite

# 5. Jalankan migrasi (membuat semua tabel)
php artisan migrate

# 6. Isi data awal: kategori & pos kas/aset contoh
php artisan db:seed

# 7. Jalankan server
php artisan serve
```

Setelah itu buka **http://localhost:8000** di browser.

> **Catatan Windows (XAMPP/Laragon):** kalau tidak familiar dengan terminal, cukup jalankan langkah 1–6 di atas lewat terminal bawaan Laragon/XAMPP, lalu akses lewat `http://localhost/kafe-keuangan/public`.

### Kalau mau pakai MySQL (bukan SQLite)

Buka `.env`, ganti:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kafe_keuangan
DB_USERNAME=root
DB_PASSWORD=
```
Buat database `kafe_keuangan` di MySQL, lalu ulangi langkah 5–6 (`migrate` dan `db:seed`).

## Alur Pemakaian yang Disarankan

1. Buka menu **Pos Kas & Aset** → sesuaikan saldo awal kas, aset tetap, kewajiban, dan modal pemilik sesuai kondisi kafe sebenarnya (data contoh dari seeder bisa diedit/dihapus).
2. Buka menu **Kategori** → sesuaikan kategori pemasukan/pengeluaran sesuai kebutuhan kafe (boleh tambah sendiri, mis. "Penjualan Kopi Susu Gula Aren").
3. Setiap hari, catat transaksi lewat menu **Catat Transaksi**.
4. Cek laporan kapan saja lewat menu **Laba Rugi**, **Arus Kas**, dan **Neraca** — tinggal pilih rentang tanggal / tanggal neracanya.

## Catatan Penting soal Neraca

Aplikasi ini memakai pencatatan sederhana (bukan akuntansi double-entry penuh):
- Saldo pos **Kas/Bank** dihitung **otomatis** dari transaksi yang tercatat.
- Saldo pos **Aset Tetap**, **Kewajiban**, dan **Modal Awal** diinput/diperbarui **manual** oleh bos, mengikuti kondisi riil (mis. saat beli mesin espresso baru atau melunasi hutang).
- **Modal akhir** di Neraca = Modal Awal + akumulasi Laba/Rugi sejak awal pencatatan sampai tanggal neraca dipilih.

Ini sudah cukup untuk kebutuhan pembukuan kafe skala kecil-menengah. Kalau ke depan butuh akuntansi penuh (jurnal umum, buku besar per akun), strukturnya sudah siap dikembangkan lebih lanjut.

## Struktur Kode Penting

```
app/Models/            -> Category, Account, Transaction
app/Http/Controllers/  -> Dashboard, Transaction, Category, Account, Report
database/migrations/   -> struktur tabel categories, accounts, transactions
database/seeders/      -> data kategori & pos akun contoh
resources/views/       -> semua tampilan (Blade), termasuk 3 laporan di resources/views/reports/
routes/web.php         -> semua rute aplikasi
```

Logika pengolahan data laporan (Laba Rugi, Arus Kas, Neraca) ada di `app/Http/Controllers/ReportController.php` — silakan sesuaikan kalau ada kebutuhan khusus dari bos.

## Login

Aplikasi sekarang dilindungi login. Akun bawaan (dibuat otomatis lewat `php artisan db:seed`):

```
Email    : admin@kafe.test
Password : kafe12345
```

Segera ganti kata sandi ini setelah pemakaian pertama (lewat `php artisan tinker` atau tambahkan halaman ubah profil nanti).

## Riwayat Perubahan

- **Ubah Profil:** halaman baru di menu "Profil Saya" (atau klik foto profil di kanan atas) untuk mengubah nama, email, dan kata sandi.
- **Ekspor CSV:** tombol "⬇ Ekspor CSV" tersedia di halaman Catat Transaksi, Laba Rugi, Arus Kas, dan Neraca. File CSV langsung bisa dibuka rapi di Excel atau Google Sheets, mengikuti filter tanggal yang sedang aktif.
- **Tampilan (UI/UX):** diganti total mengikuti gaya dashboard modern — sidebar navy gelap dengan ikon, topbar berisi judul halaman & profil pengguna, kartu putih dengan sudut membulat dan bayangan tipis, latar halaman abu-abu muda.
- **Login:** ditambahkan halaman `/login`, semua halaman lain sekarang wajib login dulu (kecuali halaman login itu sendiri).
- **Bugfix dashboard:** perbaikan error `Call to a member function max() on array` di grafik 7 hari terakhir (variabel `$grafik` sekarang diubah jadi Collection di `DashboardController`).
- **Laravel:** dinaikkan dari versi 11 ke versi 12 (11 sudah tidak ditambal untuk 2 celah keamanan tertentu oleh pembuatnya).



## Keamanan (untuk Produksi)

Sistem login dasar sudah aktif. Sebelum dipasang di server publik, tetap disarankan: ganti password default di atas, aktifkan HTTPS, dan pertimbangkan menambah fitur "lupa password" kalau nanti dipakai lebih dari satu orang.

