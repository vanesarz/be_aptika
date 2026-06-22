# Raptika Backend API

API Backend untuk aplikasi Raptika (Sistem Pendataan 6 Aplikasi di bawah Dinas Komunikasi dan Informatika Jawa Barat). Project ini dibangun menggunakan Laravel 13 dan PHP 8.4 (disesuaikan menjadi PHP 8.3/8.4 di dalam container).

Sistem ini mengelola data integrasi perangkat daerah (OPD), replikasi aplikasi, performa mentoring, data interoperabilitas (Intop), statistik dokumen Sidebar Jabar, hingga kerentanan aplikasi (vulnerabilities).

---

## Prasyarat
Sebelum memulai, pastikan Anda telah menginstal software berikut di laptop Anda:
1. **Docker Desktop** (Pastikan aplikasi Docker Desktop sudah dalam posisi Running)
2. **Git**
3. **Composer** (opsional, jika ingin menjalankan script di luar Docker)

---

## Panduan Pemasangan & Menjalankan (Docker)

Ikuti langkah-langkah di bawah ini untuk menjalankan backend di server lokal Anda:

### 1. Duplikasi File Environment `.env`
Pastikan Anda sudah memiliki file `.env` di root folder backend (`raptika-be`). Jika belum, salin dari `.env.example`:
```bash
cp .env.example .env
```

### 2. Jalankan Container Docker
Bangun (build) dan jalankan container MySQL, PHP-FPM, dan Nginx secara background:
```bash
docker compose up -d --build
```

### 3. Jalankan Migrasi & Seed Database
Lakukan inisialisasi tabel database dan data awal (seeding) ke dalam database MySQL di dalam container:
```bash
docker compose exec backend php artisan migrate:fresh --seed
```

### 4. Selesai!
Aplikasi backend Anda sekarang sudah aktif dan dapat diakses:
* **Base URL API**: http://localhost:8000/api
* **Dokumentasi Swagger API**: http://localhost:8000/api/documentation

---

## Mengelola Database MySQL

Database MySQL diekspos di port 3306 pada laptop Anda. Anda dapat mengelola data di dalamnya menggunakan aplikasi database GUI seperti DBeaver, TablePlus, atau HeidiSQL dengan kredensial berikut:

* **Host**: `127.0.0.1` atau `localhost`
* **Port**: `3306`
* **Username**: `root`
* **Password**: *(Kosongkan / Tanpa Password)*
* **Nama Database**: `db_raptika`

Or if you want to access MySQL via terminal:
```bash
docker compose exec db mysql -u root db_raptika
```

---

## Perintah Penting Lainnya

* **Menghentikan Container**:
  ```bash
  docker compose down
  ```
* **Melihat Log Aplikasi**:
  ```bash
  docker compose logs -f
  ```
* **Masuk ke CLI Container Backend**:
  ```bash
  docker compose exec backend bash
  ```
