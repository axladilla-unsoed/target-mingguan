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
DB_DATABASE=target_mingguan
DB_USERNAME=root
DB_PASSWORD=
```

---

## 🚀 Panduan Instalasi & Penggunaan

### 1. Impor Database
1. Buka **phpMyAdmin** atau tool database manager Anda (HeidiSQL, DBeaver, TablePlus, MySQL CLI).
2. Buat database baru (sesuai `DB_DATABASE` di `.env`, default: `target_mingguan`).
3. Impor file **[database.sql](database.sql)**.

### 2. Menjalankan Aplikasi

#### Opsi A: Menggunakan PHP Built-in Server
Buka terminal di folder proyek ini dan jalankan perintah:
```bash
php -S localhost:8000
```
Kemudian buka browser di [http://localhost:8000](http://localhost:8000).

#### Opsi B: Menggunakan XAMPP / Laragon
1. Pindahkan folder proyek ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Pastikan service Apache dan MySQL sudah berjalan.
3. Sesuaikan file `.env` jika username/password database berbeda.
4. Akses melalui browser di `http://localhost/target-mingguan/` (sesuaikan nama foldernya).

---

## 🛠️ Fitur yang Tersedia
- ✅ **Konfigurasi Dinamis `.env`**: Ganti host, port, database, user, dan password dengan mudah via `.env`.
- ✅ **Sinkronisasi Database MySQL**: Semua perubahan (tambah target, checklist selesai, pindah hari, hapus, reset minggu) langsung tersimpan ke database.
- ✅ **Real-time & Asynchronous**: Menggunakan `fetch()` API tanpa perlu reload halaman.
- ✅ **Progress Tracker**: Progress bar & counter otomatis terhitung sesuai pencapaian mingguan.
- ✅ **Pencegahan SQL Injection**: Seluruh query menggunakan Prepared Statements (PDO).
- ❌ **Pembersihan Fitur Lama**: Fitur `localStorage`, Backup JSON, dan Restore JSON sudah sepenuhnya dihapus sesuai permintaan.
