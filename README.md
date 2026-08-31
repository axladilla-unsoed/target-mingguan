# Target Mingguan (PHP & MySQL)

Aplikasi manajemen target dan jadwal mingguan berbasis **PHP** dan **MySQL**, dengan tampilan antarmuka modern, interaktif, dan responsif.

---

## 📁 Struktur File

- **[database.sql](database.sql)** : Skrip SQL untuk membuat database `target_mingguan`, tabel `tasks`, dan mengimpor 10 data awal dari file backup JSON.
- **[koneksi.php](koneksi.php)** : Konfigurasi koneksi database MySQL menggunakan PDO.
- **[api.php](api.php)** : Backend API RESTful untuk operasi CRUD data target mingguan.
- **[index.php](index.php)** : Halaman utama antarmuka pengguna (UI) yang terhubung langsung ke database.

---

## 🚀 Panduan Instalasi & Penggunaan

### 1. Impor Database
1. Buka **phpMyAdmin** atau tool database manager Anda (HeidiSQL, DBeaver, MySQL CLI).
2. Buat database baru bernama `target_mingguan` (atau langsung jalankan file `database.sql`).
3. Impor file **[database.sql](database.sql)**.

### 2. Pengaturan Koneksi Database
Buka file **[koneksi.php](koneksi.php)** dan sesuaikan konfigurasi jika diperlukan:
```php
$host    = '127.0.0.1';
$db_name = 'target_mingguan';
$db_user = 'root';
$db_pass = ''; // isi jika MySQL Anda menggunakan password
```

### 3. Menjalankan Aplikasi

#### Opsi A: Menggunakan PHP Built-in Server
Buka terminal di folder proyek ini dan jalankan perintah:
```bash
php -S localhost:8000
```
Kemudian buka browser di [http://localhost:8000](http://localhost:8000).

#### Opsi B: Menggunakan XAMPP / Laragon
1. Pindahkan folder proyek ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Pastikan service Apache dan MySQL sudah berjalan.
3. Akses melalui browser di `http://localhost/target-mingguan/` (sesuaikan nama foldernya).

---

## 🛠️ Fitur yang Tersedia
- ✅ **Sinkronisasi Database MySQL**: Semua perubahan (tambah target, checklist selesai, pindah hari, hapus, reset minggu) langsung tersimpan ke database.
- ✅ **Real-time & Asynchronous**: Menggunakan `fetch()` API tanpa perlu reload halaman.
- ✅ **Progress Tracker**: Progress bar & counter otomatis terhitung sesuai pencapaian mingguan.
- ✅ **Pencegahan SQL Injection**: Seluruh query menggunakan Prepared Statements (PDO).
- ❌ **Pembersihan Fitur Lama**: Fitur `localStorage`, Backup JSON, dan Restore JSON sudah sepenuhnya dihapus sesuai permintaan.
