# Sistem Informasi Perpustakaan Digital (Perpustakaan CI4)

Sistem Informasi Manajemen Perpustakaan berbasis web yang dikembangkan menggunakan framework **CodeIgniter 4** (PHP 8.2+), database **MySQL**, dan antarmuka **Bootstrap 5**.

Aplikasi ini dirancang untuk memudahkan inventarisasi buku sekolah, sirkulasi peminjaman/pengembalian, serta pencetakan laporan resmi dengan identitas sekolah dinamis.

---

## 1. Akses Halaman & Fitur Utama

Aplikasi dapat dijalankan secara lokal melalui:
*   **Halaman Utama (Katalog Publik):** [http://localhost:8080/](http://localhost:8080/)
    *   *Deskripsi:* Dapat diakses oleh tamu umum untuk mencari ketersediaan buku di perpustakaan berdasarkan judul, pengarang, atau penerbit tanpa harus login.
*   **Halaman Masuk (Login):** [http://localhost:8080/auth/login](http://localhost:8080/auth/login)
    *   *Deskripsi:* Halaman pintu masuk bagi Admin, Petugas (Staff), dan Anggota (Member).

---

## 2. Akun Pengujian (Demo Accounts)

Untuk kebutuhan pengujian fitur, berikut adalah daftar akun demo yang telah didaftarkan dalam sistem:

| Peran (Role) | Jenis Pengguna | Username | Password | Kode Anggota |
| :--- | :--- | :--- | :--- | :--- |
| **Admin** (Super Admin) | Guru | `admin` | `Zona##123` | `ADM-001` |
| **Staff** (Petugas Perpus) | Pegawai | `lina_petugas` | `Lina##123` | `STF-002` |
| **Member** (Pegawai) | Pegawai | `bambang_pegawai` | `Bambang##123` | `PEG-001` |
| **Member** (Guru) | Guru | `endang_guru` | `Endang##123` | `GRU-001` |
| **Member** (Siswa 1) | Siswa | `siti_siswa` | `Siti##123` | `SSW-001` |
| **Member** (Siswa 2) | Siswa | `rian_siswa` | `Rian##123` | `SSW-002` |

---

## 3. Matriks Hak Akses & Aturan Bisnis

### A. Hak Akses Fitur (Role Based Access Control)

| Fitur / Halaman | Admin | Staff (Petugas) | Member (Siswa/Guru/Pegawai) |
| :--- | :---: | :---: | :---: |
| Halaman Dashboard | ✔ | ✔ | ✔ |
| Cari & Lihat Katalog Buku | ✔ | ✔ | ✔ |
| Manajemen Buku (Tambah, Edit, Arsip) | ✔ | ✔ | ✘ |
| Hapus Buku (*Delete*) | ✔ | ✔ | ✘ |
| Manajemen User (Tambah, Edit, Validasi) | ✔ | ✔ (Hanya Member) | ✘ |
| Hapus User (*Delete*) | ✔ | ✘ | ✘ |
| Sirkulasi Transaksi (Peminjaman & Kembali) | ✔ (View) | ✔ (Penuh) | ✘ |
| Settle Buku Hilang (*Lost Books*) | ✔ | ✔ | ✘ |
| Laporan & Cetak Dokumen Resmi | ✔ | ✔ | ✘ |
| Pengaturan Identitas Sekolah (Logo/Kop) | ✔ | ✘ | ✘ |
| Riwayat Log Aktivitas Sistem | ✔ | ✘ | ✘ |

### B. Aturan Bisnis Peminjaman Buku (Member Specific Rules)
Sistem sirkulasi membedakan batas maksimal peminjaman dan denda keterlambatan secara otomatis berdasarkan kategori pengguna:

*   **Guru / Pegawai:**
    *   Maksimal peminjaman aktif: **5 Buku** per transaksi.
    *   Denda keterlambatan: **Rp 5.000,-** per hari.
*   **Siswa:**
    *   Maksimal peminjaman aktif: **3 Buku** per transaksi.
    *   Denda keterlambatan: **Rp 1.000,-** per hari.
*   **Ketentuan Umum:**
    *   Durasi peminjaman default: **10 hari** (Peminjaman Jangka Pendek).
    *   Anggota yang memiliki transaksi berstatus `terlambat` tidak diperbolehkan melakukan peminjaman baru sampai denda/transaksi diselesaikan.

---

## 4. Cara Menjalankan Aplikasi di Lokal

1.  **Aktifkan Laragon / MySQL Server.**
2.  **Clone / Download Repositori** ke folder `www/` Laragon Anda.
3.  **Salin file `.env`** dan sesuaikan konfigurasi database Anda.
4.  **Jalankan Migrasi & Seeder** (Opsional jika database kosong):
    ```bash
    php spark migrate
    php spark db:seed DdcSeeder
    php spark db:seed ClassSeeder
    php spark db:seed SourceFundSeeder
    php spark db:seed AdminSeeder
    php spark db:seed StaffSeeder
    php spark db:seed UserDemoSeeder
    ```
5.  **Jalankan Server Lokal:**
    ```bash
    php spark serve
    ```
    Buka [http://localhost:8080/](http://localhost:8080/) pada browser Anda.

---

## 5. Panduan Instalasi di Server (Shared Hosting / VPS)

Untuk memasang aplikasi ini di server hosting dan menghubungkannya dengan domain aktif Anda, pilih salah satu metode berikut sesuai akses server yang tersedia:

### METODE A: Menggunakan VPS (Dengan Akses SSH & Git) - *Direkomendasikan*

1. **Persiapan & Upload File**:
   * Hubungkan SSH ke VPS Anda.
   * Clone repositori ke server Anda:
     ```bash
     git clone <repository-url> /var/www/perpus_ci4
     ```
2. **Konfigurasi Web Server (Domain)**:
   * Arahkan **Document Root** domain/subdomain Anda ke folder **`public/`** di dalam proyek perpustakaan (misal `/var/www/perpus_ci4/public`). Ini sangat krusial untuk aspek keamanan CodeIgniter 4.
3. **Instal Dependensi**:
   * Masuk ke folder proyek dan jalankan Composer:
     ```bash
     composer install --no-dev --optimize-autoloader
     ```
4. **Konfigurasi Environment (`.env`)**:
   * Salin file `env.example` menjadi `.env`:
     ```bash
     cp env.example .env
     ```
   * Edit file `.env` dan sesuaikan parameter berikut:
     * `CI_ENVIRONMENT = production`
     * `app.baseURL = 'https://nama-domain-anda.com/'`
     * `database.default.hostname = localhost`
     * `database.default.database = nama_db_server`
     * `database.default.username = user_db_server`
     * `database.default.password = password_db_server`
5. **Migrasi dan Inisialisasi Database**:
   * Jalankan perintah Spark berikut untuk membuat tabel dan data master awal:
     ```bash
     php spark migrate
     php spark db:seed ClassSeeder
     php spark db:seed AdminSeeder
     php spark db:seed StaffSeeder
     php spark db:seed SourceFundSeeder
     php spark db:seed DdcSeeder
     ```
6. **Set Permission Folder Writable**:
   * Berikan hak akses menulis agar sistem log dan sesi bisa disimpan oleh web server:
     ```bash
     chmod -R 775 writable
     chown -R www-data:www-data writable
     ```

---

### METODE B: Menggunakan Shared Hosting (cPanel & phpMyAdmin - Tanpa SSH)

1. **Upload File Proyek**:
   * Compress seluruh folder proyek menjadi format `.zip` (kecuali folder `vendor/`, `node_modules/`, dan file `.env`).
   * Unggah file `.zip` tersebut ke direktori root hosting Anda (satu tingkat di luar folder `public_html` atau `public_shtml` demi alasan keamanan). Ekstrak file di lokasi tersebut.
2. **Setup Folder Publik & Domain**:
   * Pindahkan semua isi dari folder **`public/`** proyek Anda ke dalam folder utama public hosting Anda (biasanya bernama **`public_html`**).
   * Edit file **`public_html/index.php`** menggunakan File Editor cPanel. Cari baris berikut (sekitar baris ke-40):
     ```php
     $pathsPath = realpath(FCPATH . '../app/Config/Paths.php');
     ```
     Sesuaikan path/jalur tersebut agar menunjuk dengan benar ke letak folder `app/Config/Paths.php` yang berada di luar folder `public_html` tadi. Contoh:
     ```php
     $pathsPath = realpath(FCPATH . '../perpus_ci4/app/Config/Paths.php');
     ```
3. **Import Database (phpMyAdmin)**:
   * Buat database dan user baru di menu **MySQL Database Wizard** cPanel. Hubungkan user tersebut ke database dengan semua hak akses (*all privileges*).
   * Buka **phpMyAdmin** di cPanel Anda, pilih database yang telah dibuat, lalu pilih menu **Import**.
   * Unggah dan import file **`db/perpustakaan.sql`** yang paling baru dari repositori lokal Anda.
4. **Konfigurasi Environment (`.env`)**:
   * Salin file `env.example` di dalam direktori root proyek menjadi `.env`.
   * Edit file `.env` di File Manager cPanel dan sesuaikan nilai domain (`app.baseURL`), koneksi database (`database.default`), dan matikan mode development (`CI_ENVIRONMENT = production`).
5. **Set Folder Permission**:
   * Pastikan folder **`writable`** yang ada di luar `public_html` memiliki permission **755** atau **775** agar web server dapat menulis log, sesi, dan unggahan logo.

---

## 6. Panduan Update Aplikasi di Server

Setiap kali ada pembaruan kode aplikasi atau perubahan skema database dari repositori lokal, ikuti panduan update berikut:

### METODE A: Update di VPS (Dengan SSH & Git)

1. Hubungkan SSH ke VPS Anda lalu masuk ke direktori proyek.
2. Tarik perubahan kode terbaru dari repositori Git:
   ```bash
   git pull origin main
   ```
3. Update pustaka Composer jika terdapat perubahan pada file `composer.json`:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
4. **Sangat Penting!** Jalankan migrasi database untuk memperbarui struktur tabel ke versi terbaru secara otomatis tanpa merusak data yang sudah ada:
   ```bash
   php spark migrate
   ```
5. Bersihkan cache sistem jika diperlukan:
   ```bash
   php spark cache:clear
   ```

### METODE B: Update di Shared Hosting (cPanel / FTP - Tanpa SSH)

1. Unggah dan timpa (*overwrite*) file-file yang mengalami perubahan menggunakan FTP (seperti FileZilla) atau File Manager cPanel.
   * **⚠️ PERINGATAN:** Jangan menimpa file `.env` server Anda dan jangan menghapus isi folder `writable/` (terutama folder `uploads` karena berisi logo sekolah yang diunggah dan berkas-berkas penting lainnya).
2. Jika ada perubahan skema database (misalnya penambahan kolom baru dari update terbaru):
   * **Cara Otomatis (Menggunakan Cron Job)**:
     Di cPanel, buka menu **Cron Jobs**. Tambahkan satu tugas baru dengan frekuensi *Once Per Minute* berisi command berikut:
     ```bash
     cd /home/username_cpanel/perpus_ci4 && php spark migrate
     ```
     Setelah tugas berjalan satu kali dan skema terupdate, **segera hapus** tugas cron job tersebut.
   * **Cara Manual (Melalui phpMyAdmin)**:
     Anda dapat melakukan ekspor tabel yang mengalami perubahan kolom secara lokal, lalu melakukan import ulang ke database server, atau menjalankan query `ALTER TABLE` secara manual di tab SQL phpMyAdmin server.
     *(Untuk update Fase 2, jalankan query berikut jika Anda mengupdate manual):*
     ```sql
     ALTER TABLE transactions ADD COLUMN jenis_peminjaman ENUM('pendek', 'panjang') DEFAULT 'pendek' AFTER kode_transaksi;
     ALTER TABLE transaction_details ADD COLUMN book_item_id INT NULL AFTER book_id;
     ALTER TABLE transaction_details ADD CONSTRAINT fk_transaction_details_book_item FOREIGN KEY (book_item_id) REFERENCES book_items(id) ON DELETE RESTRICT ON UPDATE CASCADE;
     ```
