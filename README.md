# FitGym — Sistem Manajemen & Presensi Gym (Laravel)

> **Projek UTS Pemrograman Web Tingkat Mahir**  
> Dibuat dengan **Laravel 11**, **Bootstrap 5 (Dark Cosmic Theme)**, dan **MySQL/MariaDB**.

---

## 🌟 Fitur Utama & Pemetaan Spesifikasi

### 1. Database & Relasi (6 Tabel Berelasi via Migration)
- `users`: Autentikasi, hak akses multi-role (`admin`, `member`), status akun aktif/nonaktif, nomor kontak.
- `membership_plans`: Master data paket latihan gym (durasi hari, harga, deskripsi, status aktif).
- `vouchers`: Kupon potongan harga promo (persen / nominal rupiah, kuota pemakaian, masa berlaku).
- `payments`: Transaksi pembelian paket, kalkulasi diskon voucher, upload file bukti transfer, status persetujuan (`pending`, `approved`, `rejected`), catatan alasan penolakan, serta catatan validator admin.
- `memberships`: Langganan aktif member yang otomatis terbentuk setelah transaksi disetujui, mencatat tanggal mulai dan akhir.
- `attendances`: Catatan presensi check-in harian member, terhubung ke membership aktif dan admin yang mencatat.

### 2. Modul CRUD Lengkap (Admin)
- **CRUD Paket Membership**: Tambah, ubah, hapus paket; dilengkapi pencarian nama paket, filter status aktif/nonaktif, pengurutan kolom, dan pagination.
- **CRUD Voucher Promo**: Tambah kode unik, tentukan jenis diskon (% atau Rp), kuota maksimal, masa aktif; dilengkapi filter tipe, filter kedaluwarsa, pengurutan, dan pagination.
- **CRUD Manajemen Pengguna / Member**: Tambah akun, ubah role (Admin/Member), aktifkan/nonaktifkan akun, reset password, pencarian nama/email, dan pagination.

### 3. Validasi & Penanganan Error
- Mekanisme **FormRequest** Laravel (`PlanRequest`, `VoucherRequest`, `UserRequest`).
- Validasi unik pada kode voucher dan email member.
- Pesan error kustom berbahasa Indonesia dan penanda visual Bootstrap `is-invalid`.
- Pencegahan penghapusan data berelasi dengan `QueryException` catch dan notifikasi alert yang ramah pengguna.

### 4. Alur Transaksi & Validasi Pembayaran
- **Member**: Memilih paket membership, menerapkan kode voucher promo dengan pengecekan interaktif AJAX, melihat potongan harga langsung, dan mengunggah berkas bukti transfer (JPG, PNG, WEBP, PDF).
- **Admin**: Memeriksa antrean pembayaran `pending`, melihat pratinjau bukti transfer, menyetujui transaksi (yang otomatis mengaktifkan/memperpanjang membership dan menambah kuota terpakai voucher) atau menolak transaksi disertai alasan penolakan.

### 5. Presensi & Check-in Member
- Pencatatan presensi hanya untuk member dengan membership yang sedang aktif.
- Validasi pencegahan check-in ganda di hari yang sama.
- Riwayat presensi dilengkapi filter tanggal dan pencarian member.
- Member dapat melihat riwayat kunjungan dan total kedatangannya sendiri.

### 6. Laporan Keuangan & Penjualan
- Filter berdasarkan rentang tanggal dan jenis paket.
- Metrik agregasi: Total Pendapatan Bersih, Total Transaksi Sukses, Total Diskon Diberikan, dan Bruto.
- Rincian kontribusi pendapatan per paket latihan.
- Tabel transaksi terperinci dan tombol **Cetak Laporan** (print-friendly CSS).

### 7. Dashboard Dinamis
- **Dashboard Admin**: Ringkasan data nyata (Member Aktif, Total Member, Kunjungan Hari Ini, Pendapatan Bulan Ini, antrean pending payment, dan check-in hari ini).
- **Dashboard Member**: Status keaktifan membership, hitung mundur sisa hari aktif, total kunjungan, notifikasi status pembayaran pending/ditolak, dan riwayat presensi terbaru.

### 8. Autentikasi, Otorisasi & Keamanan
- Password di-hash aman dengan bcrypt.
- `RoleMiddleware` memproteksi rute admin (`role:admin`) dan rute member (`role:member`).
- Akses tidak sah langsung ditolak dengan halaman kustom `403 Forbidden` yang elegan.

---

## 🔑 Akun Demo Pengujian

Semua akun demo berikut telah disediakan melalui Database Seeder:

| Role | Nama | Email | Password | Keterangan |
|---|---|---|---|---|
| **Admin** | Admin FitGym | `admin@gym.com` | `password` | Akses penuh seluruh modul admin |
| **Member** | Member Setia | `member@gym.com` | `password` | Akun dengan membership aktif (Paket 3 Bulan) & riwayat presensi |
| **Member** | Budi Santoso | `budi@gym.com` | `password` | Akun dengan transaksi pending (siap untuk demo validasi admin) |
| **Member** | Siti Rahma | `siti@gym.com` | `password` | Akun dengan riwayat pembayaran ditolak |

### Kode Voucher Demo:
- `FITHEMAT` : Diskon 20% (Status: Aktif, Kuota: 50)
- `DISKON50K` : Potongan Rp 50.000 (Status: Aktif, Kuota: 20)
- `KEDALUWARSA` : Diskon Rp 25.000 (Status: Sudah Kedaluwarsa)

---

## 🚀 Panduan Instalasi & Menjalankan

### 1. Clone & Masuk Direktori
```bash
git clone <URL_REPO_GITHUB_ANDA>
cd web-gym
```

### 2. Install Dependensi PHP & Node.js
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
Salin file environment dan atur koneksi database MySQL:
```bash
cp .env.example .env
php artisan key:generate
```
Pastikan pada `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fitgym
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi & Seeder Database
```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

### 5. Build Aset Frontend
```bash
npm run build
```

### 6. Jalankan Server Pengembangan
```bash
php artisan serve
```
Buka browser di `http://localhost:8000` (otomatis diarahkan ke halaman login).

---

## 🧪 Pengujian Otomatis (Automated Testing)

Projek ini dilengkapi dengan 19 Feature & Unit Tests yang mencakup seluruh skenario penilaian:

```bash
php artisan test
```

Hasil pengujian:
```text
Pass: 19
Assertions: 78
Duration: ~1.2s
Status: ALL TESTS PASSED
```
