# Target Mingguan (PHP & MySQL)

Aplikasi manajemen target dan jadwal mingguan berbasis **PHP** dan **MySQL**, dengan tampilan antarmuka modern, interaktif, responsif, serta konfigurasi environment dinamis berbasis file `.env`.

---

## 📁 Struktur File

- **[.env](.env)** : File konfigurasi kredensial database lokal (otomatis dibaca).
- **[.env.example](.env.example)** : File contoh konfigurasi environment.
- **[database.sql](database.sql)** : Skrip SQL untuk membuat database `target_mingguan`, tabel `tasks`, dan mengimpor 10 data awal dari file backup JSON.
- **[koneksi.php](koneksi.php)** : Konfigurasi koneksi database MySQL menggunakan PDO + pembaca file `.env` otomatis.
- **[api.php](api.php)** : Backend API RESTful untuk operasi CRUD data target mingguan.
- **[index.php](index.php)** : Halaman utama antarmuka pengguna (UI) yang terhubung langsung ke database.

---

## ⚙️ Konfigurasi Environment (`.env`)

Anda dapat menyesuaikan kredensial database di file **[.env](.env)** tanpa perlu mengubah kode PHP:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=target-mingguan
DB_USERNAME=root
DB_PASSWORD=
APP_PASSWORD=admin123
```

---

## 🚀 Panduan Instalasi & Penggunaan

### 1. Impor Database
1. Buka **phpMyAdmin** atau database manager Anda (HeidiSQL, DBeaver, TablePlus, MySQL CLI).
2. Buat database baru (sesuai `DB_DATABASE` di `.env`, default: `target-mingguan`).
3. Impor file **[database.sql](database.sql)** (mencakup tabel `tasks` dan `weekly_history`).

### 2. Menjalankan Aplikasi

#### Opsi A: Menggunakan PHP Built-in Server
Buka terminal di folder proyek ini dan jalankan perintah:
```bash
php -S localhost:8000
```
Kemudian buka browser di [http://localhost:8000](http://localhost:8000). Masukkan password yang ada di `.env` (default: `admin123`).

#### Opsi B: Menggunakan XAMPP / Laragon
1. Pindahkan folder proyek ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Pastikan service Apache dan MySQL sudah berjalan.
3. Sesuaikan file `.env` jika username/password database berbeda.
4. Akses melalui browser di `http://localhost/target-mingguan/` (sesuaikan nama foldernya).

---

## 🛠️ Fitur yang Tersedia
- 🔒 **Prompt Login Sederhana**: Autentikasi berbasis password statis dari `.env` (`APP_PASSWORD`), lengkap dengan tombol Show/Hide Password dan tombol Logout.
- 🎯 **Manajemen Target Harian**: Atur tugas untuk hari Senin sampai Minggu, pindah hari dengan select dropdown, checklist selesai dengan progress bar otomatis.
- 🏁 **Selesaikan Tugas Minggu Ini**: Menggantikan tombol lama "Reset minggu", menyimpan capaian minggu ke riwayat produktivitas dan menyetel ulang checklist untuk minggu baru.
- 📊 **Tracking Produktivitas Mingguan ala GitHub**: Visualisasi heatmap 52 minggu kalender tahunan di mana makin banyak tugas yang diselesaikan pada minggu itu, warna kotak makin hijau pekat. Dilengkapi metrik KPI (Total Tugas Selesai, Minggu Tercatat, Streak Mingguan, dan Rata-rata Mingguan).
- ⚙️ **Konfigurasi Dinamis `.env`**: Konfigurasi database dan password statis tanpa menyentuh kode aplikasi.
- ⚡ **Real-time & Asynchronous**: Menggunakan fetch API asynchronous tanpa perlu reload halaman.

