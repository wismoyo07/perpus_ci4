# Sistem Informasi Perpustakaan Digital (Perpustakaan CI4)

Sistem Informasi Manajemen Perpustakaan berbasis web yang dikembangkan menggunakan framework **CodeIgniter 4** (PHP 8.2+), database **MySQL**, dan antarmuka **Bootstrap 5**.

Aplikasi ini dirancang untuk memudahkan inventarisasi buku sekolah, sirkulasi peminjaman/pengembalian, serta pencetakan laporan resmi, kartu anggota, dan label buku dengan identitas sekolah dinamis.

> **Versi terbaru & catatan perubahan:** lihat halaman [Releases](https://github.com/wismoyo07/perpus_ci4/releases).

---

## 1. Akses Halaman & Fitur Utama

Aplikasi dapat dijalankan secara lokal melalui:
*   **Halaman Utama (Katalog Publik):** [http://localhost:8080/](http://localhost:8080/)
    *   *Deskripsi:* Dapat diakses oleh tamu umum untuk mencari ketersediaan buku di perpustakaan berdasarkan judul, pengarang, atau penerbit tanpa harus login.
*   **Halaman Masuk (Login):** [http://localhost:8080/auth/login](http://localhost:8080/auth/login)
    *   *Deskripsi:* Halaman pintu masuk bagi Admin, Petugas (Staff), dan Anggota (Member).
    *   *Lupa password:* anggota menghubungi petugas, lalu petugas mereset password melalui menu **Users → Reset Sandi**.

---

## 2. Akun Pengujian (Demo Accounts)

Untuk kebutuhan pengujian fitur, berikut adalah daftar akun demo yang dibuat oleh seeder dan tersedia di `db/perpustakaan.sql`:

| Peran (Role) | Jenis Pengguna | Username | Password | Kode Anggota |
| :--- | :--- | :--- | :--- | :--- |
| **Admin** (Super Admin) | Guru | `admin` | `Zona##123` | `ADM-001` |
| **Staff** (Petugas Perpus) | Pegawai | `staff` | `Zona##123` | `STF-001` |
| **Staff** (Petugas Perpus) | Pegawai | `lina_petugas` | `Lina##123` | `STF-002` |
| **Member** (Pegawai) | Pegawai | `bambang_pegawai` | `Bambang##123` | `PEG-001` |
| **Member** (Guru) | Guru | `endang_guru` | `Endang##123` | `GRU-001` |
| **Member** (Siswa 1) | Siswa | `siti_siswa` | `Siti##123` | `SSW-001` |
| **Member** (Siswa 2) | Siswa | `rian_siswa` | `Rian##123` | `SSW-002` |

> **⚠️ Penting untuk server produksi:** akun dan password di atas bersifat publik. Setelah instalasi di server, segera ganti password akun admin/staff dan hapus atau arsipkan akun member demo.

---

## 3. Matriks Hak Akses & Aturan Bisnis

### A. Hak Akses Fitur (Role Based Access Control)

| Fitur / Halaman | Admin | Staff (Petugas) | Member (Siswa/Guru/Pegawai) |
| :--- | :---: | :---: | :---: |
| Halaman Dashboard | ✔ | ✔ | ✔ |
| Cari & Lihat Katalog Buku | ✔ | ✔ | ✔ |
| Manajemen Buku (Tambah, Edit, Arsip) | ✔ | ✔ | ✘ |
| Hapus Buku (*Delete*) | ✔ | ✔ | ✘ |
| Cetak Label Buku (QR Code) | ✔ | ✔ | ✘ |
| Manajemen User (Tambah, Edit, Validasi, Reset Sandi) | ✔ | ✔ (Hanya Member) | ✘ |
| Cetak Kartu Anggota (Barcode) | ✔ | ✔ (Hanya Member) | ✘ |
| Hapus User (*Delete*) | ✔ | ✘ | ✘ |
| Sirkulasi Transaksi (Peminjaman & Kembali) | ✔ (View) | ✔ (Penuh) | ✘ |
| Laporkan Buku Hilang (selama masa pinjam) | ✘ | ✔ | ✘ |
| Settle Buku Hilang (*Lost Books*) | ✔ | ✔ | ✘ |
| Pembayaran Denda Keterlambatan (Lunas / Dibebaskan) | ✔ | ✔ | ✘ |
| Laporan & Cetak Dokumen Resmi | ✔ | ✔ | ✘ |
| Pengaturan Identitas Sekolah (Logo/Kop, Kode Barang Buku, Batas Hari Denda) | ✔ | ✘ | ✘ |
| Data Master (Sumber Dana & Kelas DDC) | ✔ | ✘ | ✘ |
| Riwayat Log Aktivitas Sistem | ✔ | ✘ | ✘ |

### B. Aturan Bisnis Peminjaman Buku (Member Specific Rules)
Sistem sirkulasi membedakan batas maksimal peminjaman dan denda keterlambatan secara otomatis berdasarkan kategori pengguna:

*   **Guru / Pegawai:**
    *   Maksimal **5 Buku** per transaksi.
    *   Denda keterlambatan: **Rp 5.000,-** per buku per hari.
*   **Siswa:**
    *   Maksimal **3 Buku** per transaksi.
    *   Denda keterlambatan: **Rp 1.000,-** per buku per hari.
*   **Ketentuan Umum:**
    *   Durasi peminjaman default: **10 hari** (Peminjaman Jangka Pendek) atau **180 hari** (Jangka Panjang).
    *   Satu transaksi minimal berisi **1 buku**, dan anggota tidak dapat meminjam judul yang sama yang masih dipinjamnya.
    *   Anggota maksimal memiliki **3 transaksi aktif** (status `dipinjam`/`terlambat`) sekaligus.
    *   **Pengembalian per buku:** buku dalam satu transaksi bisa dikembalikan satu per satu (sebelum maupun sesudah jatuh tempo). Di halaman **Detail / Kembali**, pilih aksi tiap buku: *Dikembalikan sekarang*, *Belum dibawa*, atau *Hilang* (mulai H+1). Buku yang belum dibawa tetap dipinjam dan **dendanya terus berjalan** sampai dikembalikan. Transaksi selesai setelah semua buku dikembalikan atau dinyatakan hilang.
    *   Peminjaman berstatus **terlambat otomatis sejak H+1** setelah jatuh tempo (diperbarui setiap halaman sirkulasi/dashboard dibuka). Anggota dengan pinjaman terlambat tidak diperbolehkan meminjam lagi sampai transaksinya diselesaikan. Perintah `php spark transactions:updateLate` tetap bisa dijadwalkan harian sebagai cadangan: di Windows lewat Task Scheduler (bagian 5 langkah 8), di server lewat cron (bagian 6).
*   **Denda Keterlambatan:**
    *   Dihitung **per buku** sejak H+1 jatuh tempo, paling banyak sejumlah **batas maksimal hari** yang diatur di **Pengaturan** (default **10 hari**; isi 0 untuk tanpa batas).
    *   Denda tercatat saat buku dikembalikan dengan status **Belum Lunas**. Petugas menandainya **Lunas** atau **Dibebaskan** (wajib dengan alasan) melalui menu **Manajemen Buku → Denda Keterlambatan**.
    *   Anggota yang masih memiliki denda **belum lunas** tidak dapat melakukan peminjaman baru.
*   **Buku Hilang:**
    *   Buku baru bisa **dinyatakan hilang mulai H+1 jatuh tempo**. Saat peminjam melapor, petugas membuka transaksi (**Pinjam Kembali → Detail / Kembali**) lalu **Laporkan Hilang** dengan tanggal laporan (minimal H+1). Denda buku itu **berhenti bertambah** di tanggal laporan dan **ditahan** (tidak menghalangi peminjaman) sampai ada penyelesaian. Laporan hilang tidak bisa dibatalkan; bila buku ternyata ada, selesaikan dengan **Buku Ditemukan** di menu Buku Hilang.
    *   **Laporan dini** (sebelum H+1, alasan wajib): denda langsung sebesar **batas maksimal hari × tarif**, langsung ditagih, dan **tetap berlaku** walaupun buku nanti ditemukan atau diganti. Aturan ini mencegah buku dinyatakan hilang lebih awal hanya untuk menghindari denda. Laporan dini membutuhkan batas maksimal hari denda (bukan 0).
    *   Penyelesaian di menu **Buku Hilang** menentukan denda keterlambatan buku itu:
        *   **Buku ditemukan** → salinan kembali ke rak; denda sampai tanggal laporan **tetap dibayar**.
        *   **Ganti buku baru** atau **ganti uang** → denda keterlambatan **dihapus**. Salinan pengganti dicatat dengan sumber dana **PRIBADI** (Pembelian Pribadi, karena dibeli peminjam) dan tahun penggantian, sehingga kode inventarisnya berprefiks `… / PRIBADI / tahun / …`.
        *   **Pemutihan** → sesuai surat keputusan: denda **dihapus**, **dibayar sebagian**, atau **dibayar penuh** (nomor/isi surat wajib dicatat).

### C. Cetak Kartu Anggota & Label Buku
*   **Kartu anggota:** menu **Users**, centang anggota lalu klik **Cetak Kartu** (atau tombol **Kartu** per baris). Ukuran kartu 85,6 × 54 mm (10 kartu per A4) dengan barcode NIS/NIP.
*   **Label buku:** menu **Buku → Salinan**, klik **Cetak Label**. Ukuran label 62 × 32 mm (24 label per A4) dengan QR code berisi kode inventaris dan nomor panggil (jika buku sudah diklasifikasi DDC). Salinan berstatus hilang tidak ikut dicetak.
*   Barcode dan QR code dibuat di server (SVG), sehingga pencetakan tidak memerlukan koneksi internet ke layanan pihak ketiga.

### D. Penomoran Buku & Klasifikasi DDC
*   **Kode buku** dibuat otomatis dengan format `kode barang / sumber dana / tahun / nomor urut`, misalnya `03.11.01.01.001 / APBD / 2025 / 001`.
    *   **Kode barang** diatur di menu **Pengaturan** (default `03.11.01.01.001`).
    *   **Sumber dana** dan **tahun pengadaan** dipilih saat menambah buku. Nomor urut berjalan per kombinasi kode barang, sumber dana, dan tahun.
*   **Kode inventaris salinan** = kode buku + nomor salinan (`… / 001 / 003`). Jika salinan baru memakai sumber dana atau tahun yang berbeda dari kode bukunya, kode salinan mengikuti sumber dana dan tahun salinan tersebut (misalnya `03.11.01.01.001 / APBN / 2026 / 002 / 006`) tanpa bentrok dengan kode lain.
*   **Klasifikasi DDC** tersedia dalam tiga tingkat, ditentukan dari kodenya:
    *   **Kelas utama** `X00` (10 kelas, misalnya 300 Ilmu Sosial),
    *   **Divisi** `XY0` (misalnya 370 Pendidikan, 950 Sejarah Asia),
    *   **Seksi** untuk kode lainnya (misalnya 297 Islam).
*   Per buku, pilih klasifikasi di dropdown (dikelompokkan per kelas utama) dan, bila perlu, isi **nomor DDC rinci** (misalnya `371.3`). Jika nomor DDC diisi, klasifikasi dipilih otomatis dari yang paling spesifik: seksi → divisi → kelas (`297.4` → 297 Islam, `371.3` → 370 Pendidikan, `005.133` → 000).
*   **Nomor panggil** dibuat otomatis dari nomor DDC + 3 huruf pertama pengarang + huruf pertama judul, misalnya `005.133 AND p`. Ditampilkan di daftar buku, katalog, dan label.
*   **Data Master** (menu admin): kelola daftar sumber dana dan klasifikasi DDC (kelas, divisi, seksi). Data tidak dihapus permanen, cukup dinonaktifkan. Kode sumber dana tidak bisa diubah setelah dibuat karena sudah tertulis di kode buku.
*   Laporan menampilkan sebaran judul dan eksemplar per kelas utama (gabungan semua divisi & seksinya), rincian per divisi/seksi, dan buku yang **belum diklasifikasi**.

---

## 4. Kebutuhan Sistem

*   **PHP 8.2** atau lebih baru dengan ekstensi `intl`, `mbstring`, dan `mysqli`.
*   **MySQL 8** (dikembangkan dan diuji dengan MySQL 8.0.30).
*   **Composer 2** (untuk memasang dependensi; folder `vendor/` tidak disertakan di repositori).

---

## 5. Cara Menjalankan Aplikasi di Lokal

1.  **Aktifkan Laragon / MySQL Server.**
2.  **Clone Repositori** ke folder `www/` Laragon Anda:
    ```bash
    git clone https://github.com/wismoyo07/perpus_ci4.git
    cd perpus_ci4
    ```
3.  **Instal Dependensi** (wajib, membuat folder `vendor/`):
    ```bash
    composer install
    ```
4.  **Siapkan file `.env`:** salin `env.example` menjadi `.env`, lalu sesuaikan `app.baseURL` (misalnya `'http://localhost:8080/'`) dan koneksi database (`database.default.*`).
5.  **Buat database** kosong (misalnya `perpus_ci4`), lalu **jalankan migrasi & seeder**:
    ```bash
    php spark migrate
    php spark db:seed DdcSeeder
    php spark db:seed ClassSeeder
    php spark db:seed SourceFundSeeder
    php spark db:seed AdminSeeder
    php spark db:seed StaffSeeder
    php spark db:seed UserDemoSeeder
    php spark db:seed BookSeeder
    ```
    > **⚠️ Perhatian:** `BookSeeder` mengosongkan (*truncate*) tabel `books` dan `book_items` sebelum mengisi data contoh, jadi **jangan jalankan pada database yang sudah berisi data buku**. Seeder data master (`DdcSeeder`, `ClassSeeder`, `SourceFundSeeder`) dan seeder akun aman dijalankan ulang: data yang sudah ada dilewati. Sebagai alternatif, import `db/perpustakaan.sql` yang berisi hasil yang sama.
6.  **Jalankan Server Lokal:**
    ```bash
    php spark serve
    ```
    Buka [http://localhost:8080/](http://localhost:8080/) pada browser Anda.
7.  **Menjalankan Tes** (opsional):
    ```bash
    php vendor/bin/phpunit --no-coverage
    ```
8.  **Jadwal Pembaruan Status Terlambat di Windows** (opsional):
    Status `terlambat` sudah diperbarui otomatis setiap halaman sirkulasi/dashboard dibuka, jadi langkah ini **tidak wajib**. Berguna bila aplikasi dipasang di PC Windows yang selalu menyala (misalnya komputer perpustakaan) dan Anda ingin status selalu terbarui walau tidak ada yang membuka aplikasi. Jalankan di PowerShell atau Command Prompt, sesuaikan folder proyek dan lokasi `php.exe`:
    ```bat
    schtasks /Create /SC DAILY /ST 00:05 /TN "perpus_ci4 updateLate" /TR "cmd /c cd /d C:\laragon\www\perpus_ci4 && C:\laragon\bin\php\php-8.x\php.exe spark transactions:updateLate"
    ```
    *   Tugas tampil di **Task Scheduler** (`taskschd.msc`) → **Task Scheduler Library** → *perpus_ci4 updateLate*.
    *   Tugas hanya berjalan saat pengguna Windows sedang login, komputer menyala, dan MySQL aktif pada jam tersebut. Agar jadwal yang terlewat tetap dijalankan, buka properti tugas → tab **Settings** → centang *Run task as soon as possible after a scheduled start is missed*.
    *   Uji dan periksa hasil (*Last Result* `0` = berhasil):
        ```bat
        schtasks /Run /TN "perpus_ci4 updateLate"
        schtasks /Query /TN "perpus_ci4 updateLate" /V /FO LIST
        ```
    *   Hapus jadwal: `schtasks /Delete /TN "perpus_ci4 updateLate" /F`
    *   Untuk server Linux/cPanel gunakan **cron** (lihat bagian 6).

---

## 6. Panduan Instalasi di Server (Shared Hosting / VPS)

Untuk memasang aplikasi ini di server hosting dan menghubungkannya dengan domain aktif Anda, pilih salah satu metode berikut sesuai akses server yang tersedia:

### METODE A: Menggunakan VPS (Dengan Akses SSH & Git) - *Direkomendasikan*

1. **Persiapan & Upload File**:
   * Hubungkan SSH ke VPS Anda.
   * Clone repositori ke server Anda (gunakan tag rilis terbaru untuk versi stabil):
     ```bash
     git clone https://github.com/wismoyo07/perpus_ci4.git /var/www/perpus_ci4
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
   * Biarkan `APP_PASSWORD_PEPPER` sama dengan nilai di `env.example` **sebelum** akun pertama dibuat. Mengubah nilai ini membuat semua password yang sudah ada (termasuk akun dari seeder / `db/perpustakaan.sql`) tidak bisa dipakai login.
5. **Migrasi dan Inisialisasi Database**:
   * Jalankan perintah Spark berikut untuk membuat tabel dan data master awal (pada database kosong):
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
7. **Jadwalkan Pembaruan Status Terlambat (Cron)**:
   * Tambahkan cron harian (misalnya setiap pukul 00:05):
     ```bash
     5 0 * * * cd /var/www/perpus_ci4 && php spark transactions:updateLate >> /dev/null 2>&1
     ```

---

### METODE B: Menggunakan Shared Hosting (cPanel & phpMyAdmin - Tanpa SSH)

1. **Siapkan Paket di Komputer Lokal**:
   * Karena server tanpa SSH tidak bisa menjalankan Composer, pasang dependensi produksi di komputer lokal terlebih dahulu:
     ```bash
     composer install --no-dev --optimize-autoloader
     ```
   * Compress seluruh folder proyek menjadi format `.zip` **termasuk folder `vendor/`**, tetapi **tanpa** file `.env`, folder `.git/`, dan isi folder `writable/` (log, sesi, cache).
2. **Upload File Proyek**:
   * Unggah file `.zip` tersebut ke direktori root hosting Anda (satu tingkat di luar folder `public_html` atau `public_shtml` demi alasan keamanan). Ekstrak file di lokasi tersebut.
3. **Setup Folder Publik & Domain**:
   * Pindahkan semua isi dari folder **`public/`** proyek Anda ke dalam folder utama public hosting Anda (biasanya bernama **`public_html`**).
   * Edit file **`public_html/index.php`** menggunakan File Editor cPanel. Cari baris berikut (sekitar baris ke-40):
     ```php
     $pathsPath = realpath(FCPATH . '../app/Config/Paths.php');
     ```
     Sesuaikan path/jalur tersebut agar menunjuk dengan benar ke letak folder `app/Config/Paths.php` yang berada di luar folder `public_html` tadi. Contoh:
     ```php
     $pathsPath = realpath(FCPATH . '../perpus_ci4/app/Config/Paths.php');
     ```
4. **Import Database (phpMyAdmin)**:
   * Buat database dan user baru di menu **MySQL Database Wizard** cPanel. Hubungkan user tersebut ke database dengan semua hak akses (*all privileges*).
   * Buka **phpMyAdmin** di cPanel Anda, pilih database yang telah dibuat, lalu pilih menu **Import**.
   * Unggah dan import file **`db/perpustakaan.sql`**. File ini berisi struktur tabel terbaru, data master (kelas, DDC, sumber dana), akun demo, dan katalog buku contoh.
   * Setelah login pertama, ganti password akun admin dan hapus data/akun demo yang tidak diperlukan (lihat bagian 2).
5. **Konfigurasi Environment (`.env`)**:
   * Salin file `env.example` di dalam direktori root proyek menjadi `.env`.
   * Edit file `.env` di File Manager cPanel dan sesuaikan nilai domain (`app.baseURL`), koneksi database (`database.default`), dan matikan mode development (`CI_ENVIRONMENT = production`). Jangan ubah `APP_PASSWORD_PEPPER` (lihat Metode A langkah 4).
6. **Set Folder Permission**:
   * Pastikan folder **`writable`** yang ada di luar `public_html` memiliki permission **755** atau **775** agar web server dapat menulis log, sesi, dan unggahan logo.
7. **Jadwalkan Pembaruan Status Terlambat (Cron)**:
   * Di cPanel, buka menu **Cron Jobs** dan tambahkan tugas harian (*Once Per Day*):
     ```bash
     cd /home/username_cpanel/perpus_ci4 && php spark transactions:updateLate
     ```

---

## 7. Panduan Update Aplikasi di Server

Setiap kali ada rilis baru, baca dulu catatan rilisnya di halaman [Releases](https://github.com/wismoyo07/perpus_ci4/releases), lalu ikuti panduan berikut:

### METODE A: Update di VPS (Dengan SSH & Git)

1. Hubungkan SSH ke VPS Anda lalu masuk ke direktori proyek.
2. Tarik perubahan kode terbaru dari repositori Git:
   ```bash
   git pull origin main
   ```
3. Pasang/perbarui pustaka Composer (wajib jika `composer.json` atau `composer.lock` berubah):
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
   * Jika `composer.lock` berubah pada rilis tersebut, jalankan `composer install --no-dev --optimize-autoloader` di komputer lokal lalu unggah ulang **seluruh folder `vendor/`**.
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
