# Analisis Penerapan VCS (Version Control System) & GitHub pada Proyek Perpustakaan CI4

Laporan ini menganalisis penerapan sistem kontrol versi pada proyek **Perpustakaan CI4** menggunakan Git dan GitHub, mengidentifikasi temuan penting (termasuk masalah keamanan), serta memberikan rekomendasi praktik terbaik (*best practices*) untuk pengembangan tim.

---

## 1. Klarifikasi Istilah: CVS vs. VCS/Git
Sebelum masuk ke analisis teknis, penting untuk membedakan istilah berikut:
- **CVS (Concurrent Versions System):** Merupakan salah satu perangkat lunak kontrol versi tersentralisasi generasi lama (populer di era 1980–1990-an) yang saat ini sudah sangat jarang digunakan.
- **VCS (Version Control System):** Adalah kategori umum untuk sistem kontrol versi.
- **Git:** Merupakan sistem kontrol versi terdistribusi (Distributed VCS) modern yang menjadi standar industri saat ini dan digunakan dalam proyek perpustakaan ini.
- **GitHub:** Adalah platform berbasis web yang meng-host repositori Git di cloud serta menyediakan fitur kolaborasi seperti Pull Request, Issue Tracking, dan CI/CD (GitHub Actions).

---

## 2. Kondisi Repositori Git Saat Ini
Berdasarkan investigasi terhadap repositori proyek di direktori `d:/Development/laragon/www/perpus_ci4`, berikut adalah fakta-fakta teknis yang ditemukan:

### A. Konfigurasi Remote & Branching
- **Remote Repository:** Terhubung ke GitHub dengan alamat [https://github.com/wismoyo07/perpus_ci4.git](https://github.com/wismoyo07/perpus_ci4.git).
- **Nama Branch Lokal:** Proyek ini saat ini berada di branch lokal bernama `master`.
- **Nama Branch Remote:** Remote repositori (GitHub) memiliki branch default bernama `main` dan juga `master` (seperti yang terlihat pada output `remotes/origin/main` dan `remotes/origin/master`).
- **Status Sinkronisasi:** Branch lokal `master` terdeteksi lebih maju **6 commit** dibandingkan `origin/main` (`Your branch is ahead of 'origin/main' by 6 commits`). Hal ini disebabkan branch lokal `master` dikonfigurasi untuk melacak (*track*) branch remote `origin/main` secara langsung. Perbedaan nama ini berpotensi membingungkan.

### B. Riwayat Commit Terakhir
Grafik riwayat commit menunjukkan adanya percabangan dan merge commit sebelumnya:
- **`c70d58d`** - Update Form Login dan Register (Terbaru di lokal)
- **`231f3dc`** - Penyesuaian Registrasi dan Login dengan Table users baru
- **`659e060`** - Update Database, migrate, and Seeder
- **`12aa1a0`** - Update Migrate
- **`60e0daa`** - Perpus CI4 (Merge commit)

---

## 3. Temuan Kritis & Analisis Keamanan

> [!CAUTION]
> ### Kebocoran File `.env` ke Git (Masalah Keamanan Kritis)
> File konfigurasi lingkungan [`.env`](file:///d:/Development/laragon/www/perpus_ci4/.env) saat ini **terlacak (*tracked*) oleh Git** dan memiliki status modifikasi (*modified*). 
> 
> **Mengapa ini berbahaya?**
> Meskipun `.env` sudah dimasukkan ke dalam [`.gitignore`](file:///d:/Development/laragon/www/perpus_ci4/.gitignore#L44), Git akan tetap melacak perubahan pada file tersebut jika file tersebut sudah telanjur dimasukkan ke dalam repositori sebelum aturan ignore dibuat. Jika file ini di-push ke GitHub, informasi sensitif berikut akan terekspos secara publik/bersama:
> - Kunci enkripsi / Pepper: `APP_PASSWORD_PEPPER=R4nd0mStr1ng_SuperSecret_2026`
> - Kredensial Database Lokal: Hostname, Username (`root`), dan Password database.

> [!WARNING]
> ### Script Pengubah Database di Folder Publik (`public/`)
> Terdapat beberapa file script PHP di direktori publik yang saat ini berstatus untracked (tidak dilacak oleh Git):
> - [`public/import_db.php`](file:///d:/Development/laragon/www/perpus_ci4/public/import_db.php)
> - [`public/run_alter.php`](file:///d:/Development/laragon/www/perpus_ci4/public/run_alter.php)
> - [`public/check_books_table.php`](file:///d:/Development/laragon/www/perpus_ci4/public/check_books_table.php)
> 
> Menaruh script manipulasi database langsung di folder `public/` sangat berbahaya jika aplikasi dideploy ke server produksi, karena script tersebut dapat diakses secara bebas oleh siapa saja lewat browser (contoh: `http://domain.com/run_alter.php`) tanpa autentikasi, yang bisa memicu kerusakan atau manipulasi data sepihak.

---

## 4. Rekomendasi Solusi & Praktik Terbaik (Best Practices)

Berikut adalah langkah-langkah konkret yang direkomendasikan untuk menata ulang penerapan VCS Git dan GitHub pada aplikasi Perpustakaan ini:

### Langkah 1: Hapus Pelacakan File `.env` tanpa Menghapusnya Secara Fisik
Jalankan perintah berikut di terminal repositori lokal untuk menghapus `.env` dari index pelacakan Git (tapi tetap menyimpannya di komputer lokal Anda):
```bash
git rm --cached .env
git commit -m "security: stop tracking .env file and keep it local"
```
Setelah dicommit, file `.env` akan sepenuhnya diabaikan oleh Git sesuai dengan aturan di `.gitignore`. Tim developer lain cukup menyalin dari `env.example` dan mengisinya secara mandiri di komputer masing-masing.

### Langkah 2: Sinkronisasi dan Ganti Nama Branch Lokal ke `main`
Untuk mengikuti standar modern GitHub dan menghindari kebingungan nama branch (`master` lokal melacak `main` remote), ganti nama branch lokal Anda menjadi `main` dengan perintah:
```bash
# Ganti nama branch lokal aktif menjadi 'main'
git branch -m master main

# Hubungkan kembali (set upstream) branch lokal 'main' ke 'origin/main' di GitHub
git branch -u origin/main main
```

### Langkah 3: Amankan/Hapus Script Database dari Folder `public/`
- **Gunakan Migrasi:** Seluruh modifikasi skema database sebaiknya dilakukan melalui mekanisme **CodeIgniter 4 Migrations** yang sudah tersedia di folder [`app/Database/Migrations/`](file:///d:/Development/laragon/www/perpus_ci4/app/Database/Migrations).
- **Hapus File Uji Coba:** Hapus file `import_db.php` dan `run_alter.php` dari folder `public/`. Jika masih dibutuhkan untuk kebutuhan lokal, pindahkan ke folder di luar `public/` (misalnya folder `db/` atau root project) dan masukkan nama filenya ke dalam `.gitignore`.

### Langkah 4: Terapkan Alur Kerja Kolaborasi (GitHub Flow)
Jika proyek ini dikembangkan secara tim, hindari melakukan push langsung ke branch utama (`main`). Terapkan alur kerja berikut:
1. **Buat Branch Fitur/Bugfix:** Buat branch baru dari `main` sebelum menulis kode.
   ```bash
   git checkout -b feature/sistem-nomor-buku
   ```
2. **Lakukan Commit Secara Atomik:** Commit perubahan dengan pesan yang jelas.
3. **Push ke GitHub & Buat Pull Request (PR):**
   ```bash
   git push origin feature/sistem-nomor-buku
   ```
   Buka GitHub dan ajukan Pull Request (PR) ke branch `main`.
4. **Code Review:** Rekan tim melakukan review terhadap kode di PR tersebut sebelum digabungkan (*merge*).

### Langkah 5: Penerapan CI/CD dengan GitHub Actions
Karena proyek ini menggunakan PHPUnit untuk pengujian (terdefinisi di [`composer.json`](file:///d:/Development/laragon/www/perpus_ci4/composer.json#L19-L20) dan `phpunit.xml.dist`), Anda dapat membuat alur integrasi berkelanjutan (*Continuous Integration*) menggunakan GitHub Actions.

Buat file alur kerja baru di `.github/workflows/ci.yml` untuk menjalankan pengujian otomatis setiap kali ada Push atau Pull Request ke branch `main`:
```yaml
name: CI - PHPUnit Test

on:
  push:
    branches: [ "main" ]
  pull_request:
    branches: [ "main" ]

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
    - uses: actions/checkout@v3

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        extensions: mbstring, intl, simplexml, mysql

    - name: Copy env
      run: cp env.example .env

    - name: Install Dependencies
      run: composer install --no-ansi --no-interaction --no-scripts --prefer-dist

    - name: Run PHPUnit
      run: vendor/bin/phpunit
```

---

## 5. Kesimpulan
Penerapan Git dan GitHub pada aplikasi perpustakaan ini sudah berjalan (terbukti dengan adanya commit history dan remote repository). Namun, terdapat **celah keamanan kritis** berupa pelacakan file `.env` dan keberadaan script database publik yang harus segera ditangani sebelum proyek diunggah lebih jauh atau dideploy ke server produksi. 

Dengan mengikuti rekomendasi perbaikan di atas, repositori proyek perpustakaan ini akan menjadi jauh lebih aman, rapi, dan siap untuk kolaborasi tim tingkat lanjut.
