# SPLPD Apps 🚀

**SPLPD Apps** adalah Sistem Informasi Manajemen untuk Pelayanan Publik dan Dokumen (SPLPD), yang dirancang menggunakan teknologi web modern untuk memudahkan pengelolaan data instansi, pendaftaran pengguna, dan manajemen konten (CMS).

Aplikasi ini dibangun menggunakan framework **Laravel 12** yang powerful, dipadukan dengan **Bootstrap 5** untuk tampilan yang responsif, serta **DataTables** untuk manajemen data yang interaktif.

---

## 📋 Persyaratan Sistem

Sebelum menginstal, pastikan server atau komputer Anda memiliki:

-   **PHP**: Versi 8.2 atau lebih baru.
-   **Composer**: Dependency manager untuk PHP.
-   **Node.js & NPM**: Versi Terbaru.
-   **Database**: MySQL.

---

## 💻 Panduan Instalasi (Environment Development)

Ikuti langkah berikut untuk menjalankan aplikasi di komputer lokal (Laptop/PC) untuk keperluan coding atau testing.

### 1. Clone Repository
Unduh kode sumber dari GitHub:
```bash
git clone https://github.com/ishaqhabibi/SPLPD-APPS.git
cd SPLPD-APPS
```

### 2. Install Dependency
Install library PHP dan JavaScript yang dibutuhkan:
```bash
# Install library PHP
composer install

# Install library JavaScript
npm install
```

### 3. Konfigurasi Environment
Duplikasi file `.env.example` menjadi `.env` dan atur koneksi database:
```bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan dengan database lokal Anda:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Key & Migrasi Database
Jalankan perintah berikut untuk mengunci aplikasi dan memasukkan struktur tabel ke database:
```bash
php artisan key:generate
php artisan migrate --seed
```
*(Option `--seed` akan mengisi database dengan data awal / master data seperti admin default).*

### 5. Jalankan Aplikasi
Jalankan perintah berikut untuk menyalakan server Laravel dan Vite secara bersamaan:
```bash
npm run dev:all
```
*(Perintah ini akan menjalankan `php artisan serve` dan `npm run dev` dalam satu terminal).*

Buka browser dan akses alamat: `http://127.0.0.1:8000`

---

## 🚀 Panduan Instalasi (Environment Production)

Gunakan langkah ini jika aplikasi akan di-upload ke server hosting atau VPS (untuk diakses publik).

### 1. Upload & Konfigurasi
-   Upload seluruh file ke server.
-   Jalankan `composer install --optimize-autoloader --no-dev`.
-   Atur file `.env` (pastikan `APP_ENV=production` dan `APP_DEBUG=false`).

### 2. Build Aset Frontend
Alih-alih `npm run dev`, jalankan perintah build untuk mengompres aset agar ringan:
```bash
npm install
npm run build
```
*(Folder `public/build` akan terisi file CSS/JS siap pakai).*

### 3. Optimasi Cache
Jalankan perintah ini di server untuk performa maksimal:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Setup Web Server
Arahkan dokumen root web server (Nginx/Apache) ke folder **`/public`** di dalam proyek.

---

## 🔐 Akun Default

Gunakan akun berikut untuk login pertama kali:

### Super Admin
-   **Email**: `ishaqhabibi@lumajangkab.go.id`
-   **Password**: `password`

### Admin Dinas
-   **Email**: `habibi@gmail.com`
-   **Password**: `password`

---
**Hak Cipta © 2026 SPLPD Apps Team.**
