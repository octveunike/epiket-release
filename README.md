# EPIKET

**EPIKET** (e-Piket) is a web-based student attendance and piket (duty)
management system built for school use. It supports daily class attendance,
dispensation records, guest logs, tardiness records, and role-based dashboards
for administrators, piket officers, and homeroom teachers.

---

## Application Information

| Item                | Detail                                   |
| ------------------- | ---------------------------------------- |
| **Application Name** | EPIKET (e-Piket)                        |
| **Version**          | 1.0.0                                   |
| **Last Author**      | Eunike Octavia                          |
| **Year**             | 2026                                    |
| **Framework**        | Laravel 10 (`^10.10`)                   |
| **PHP Version**      | PHP 8.1 or higher (developed on PHP 8.2)|
| **Database**         | MySQL                                   |
| **Frontend Build**   | Vite                                    |

---

## Fitur Aplikasi

Berikut daftar fitur utama yang tersedia pada aplikasi **EPIKET**:

- **Autentikasi & Hak Akses Berbasis Peran** — login pengguna dengan pembagian hak akses untuk 4 peran: **Admin**, **Petugas Piket**, **Wali Kelas**, dan **Ketua Kelas**.
- **Dashboard per Peran** — tampilan ringkasan yang berbeda dan sesuai kebutuhan tiap peran (rekap absensi harian, kelas yang belum absen, dispensasi menunggu, keterlambatan, dsb.).
- **Manajemen Absensi** — input absensi harian oleh Ketua Kelas, pengisian detail per siswa (Hadir/Izin/Sakit/Alpha), serta **validasi berjenjang** oleh Wali Kelas / Petugas Piket, lengkap dengan riwayat validasi.
- **Manajemen Dispensasi** — pengajuan dispensasi kegiatan, penambahan siswa peserta, alur **ajukan → verifikasi → revisi**, dan pemantauan status.
- **Pencatatan Keterlambatan** — pencatatan siswa yang terlambat beserta alasannya.
- **Buku Tamu (Daftar Tamu)** — pencatatan kunjungan tamu sekolah beserta **ekspor data**.
- **Manajemen Kelas** — pengelolaan data kelas beserta wali kelas dan ketua kelas.
- **Periode Akademik** — pengelolaan tahun ajaran / periode akademik aktif.
- **Manajemen Guru** — data guru dengan dukungan **impor dari Excel**.
- **Manajemen Siswa** — data siswa dengan dukungan **impor dari Excel**.
- **Manajemen Staff** — data staff dengan dukungan **impor dari Excel**.
- **Manajemen Organisasi** — data organisasi beserta anggota, dengan dukungan **impor dari Excel**.
- **Laporan** — rekap laporan absensi & keterlambatan dengan dukungan **ekspor ke Excel**.
- **Manajemen Pengguna** — pengelolaan akun pengguna beserta penetapan peran.

---

## Kebutuhan Sistem

Pastikan perangkat lunak berikut sudah terpasang sebelum instalasi:

- PHP 8.1 atau lebih baru
- Composer
- MySQL
- Node.js & npm

---

## Cara Menjalankan Aplikasi

### 1. Instal dependency PHP

```bash
composer install
```

### 2. Instal dependency frontend

```bash
npm install
```

### 3. Salin file environment

```bash
cp .env.example .env
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Konfigurasi database

Buat database MySQL (misalnya `db_epiket`), lalu sesuaikan pengaturan database
pada file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_epiket
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Siapkan database

Jalankan migrasi dan seeder untuk membuat struktur tabel serta data dasar
(role, akun bawaan, kelas, status absensi, periode akademik, dll.):

```bash
php artisan migrate
php artisan db:seed
```

**Opsional — muat data contoh.** Untuk mengisi aplikasi dengan data dummy
sebagai bahan uji coba atau demonstrasi, impor file SQL berikut *setelah*
menjalankan migrasi dan seeder di atas:

```bash
mysql -u root -p db_epiket < database/dummies.sql
```

### 7. Build aset frontend

Untuk produksi:

```bash
npm run build
```

Atau saat pengembangan (dengan hot reloading):

```bash
npm run dev
```

### 8. Jalankan aplikasi

```bash
php artisan serve
```

Aplikasi akan berjalan di **http://localhost:8000**.

---

## Login Default

Setelah seeding, akun bawaan yang tersedia adalah **admin / admin**.
Silakan ganti kredensial default sebelum aplikasi digunakan pada lingkungan
sebenarnya.
