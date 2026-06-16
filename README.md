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
