# InApp Inventory System

Aplikasi InApp Inventory System adalah platform manajemen inventaris dan pengadaan barang yang dibangun menggunakan arsitektur *microservices* sederhana:
- **Frontend**: Laravel 11 + Bootstrap (Blade Templates)
- **Backend**: Node.js + Express + Sequelize ORM
- **Database**: MySQL

---

## 🛠️ Persyaratan Sistem (Prerequisites)

Sebelum menjalankan proyek ini, pastikan komputer Anda telah terinstal:
- **PHP** (minimal versi 8.1+) & **Composer**
- **Node.js** (minimal versi 16+) & **npm**
- **MySQL** (XAMPP / Laragon / MySQL Server)
- **Git**

---

## 🚀 Panduan Instalasi (Cara Menjalankan Aplikasi)

Ikuti langkah-langkah di bawah ini secara berurutan setelah melakukan *clone* repository:

### 1. Clone Repository
```bash
git clone <url-repository-anda>
cd PWL-CP2
```

### 2. Setup Database (MySQL Workbench)
Aplikasi ini sudah menyediakan desain *database* bawaan dalam format `.mwb` (MySQL Workbench).
1. Buka aplikasi **MySQL Workbench**.
2. Buka file desain database `Project_CP2_Final.mwb` yang tersedia di dalam folder repositori ini
3. Lakukan **Forward Engineer** dengan cara klik menu: `Database` -> `Forward Engineer...`
4. Ikuti *wizard* (klik *Next*) hingga proses selesai. Proses ini akan otomatis membuatkan *database* beserta seluruh tabel, kolom, dan relasi (*foreign key*) secara sempurna.
5. Pastikan nama *database* yang terbuat sesuai dengan konfigurasi `.env` Anda nantinya.

### 3. Setup Backend (Node.js)
Buka terminal baru dan arahkan ke folder backend:
```bash
cd backend-node.js
```
1. Install semua *dependencies*:
   ```bash
   npm install
   ```
2. Konfigurasi Environment:
   - *Copy* file `.env.example` (jika ada) menjadi `.env`.
   - Sesuaikan konfigurasi koneksi database MySQL Anda di dalam file `.env`:
     ```env
     DB_HOST=localhost
     DB_USER=root
     DB_PASSWORD=
     DB_NAME=db_inventaris
     PORT=5000
     ```
3. Jalankan server backend:
   ```bash
   npm run dev
   ```
   *Catatan: Pastikan terminal menunjukkan "Database terhubung." dan server berjalan di port 5000.*

### 4. Setup Frontend (Laravel)
Buka terminal baru dan arahkan ke folder frontend:
```bash
cd frontend-laravel
```
1. Install *dependencies* PHP dan Node.js:
   ```bash
   composer install
   npm install
   ```
2. Konfigurasi Environment:
   - *Copy* file `.env.example` menjadi `.env`:
     ```bash
     cp .env.example .env
     ```
   - *Generate* Application Key Laravel:
     ```bash
     php artisan key:generate
     ```
   - Pastikan URL Backend API terhubung dengan benar di dalam `.env` Laravel:
     ```env
     API_URL=http://localhost:5000/api
     ```
3. Jalankan Laravel Server:
   ```bash
   php artisan serve
   ```
   *(Aplikasi akan berjalan di http://127.0.0.1:8000)*
4. Jalankan Vite Asset Bundler (Biarkan berjalan di background):
   ```bash
   npm run dev
   ```

---

## 🔑 Akun Default (Testing)
Jika Anda menggunakan *seeder* bawaan, Anda dapat login ke aplikasi menggunakan akun berikut:
Role Admin
- **Email:** `admin@gmail.com`
- **Password:** `password`

Role Kepala Laboratorium
- **Email:** `kalab@gmail.com`
- **Password:** `password`

Role Ketua Program Studi
- **Email:** `kaprodi@gmail.com`
- **Password:** `password`

Role Staf Administrasi
- **Email:** `stafadmin@gmail.com`
- **Password:** `password`

Role staf Laboratorium
- **Email:** `staflab@gmail.com`
- **Password:** `password`

---


