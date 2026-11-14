# Panduan Kontribusi

Terima kasih sudah tertarik membantu **Perpustakaan CI4**. Kontribusi apa pun kami sambut: laporan bug, usulan fitur, perbaikan dokumentasi, maupun kode.

## Melaporkan Bug atau Mengusulkan Fitur

1. Cek dulu daftar [Issues](https://github.com/wismoyo07/perpus_ci4/issues), siapa tahu sudah pernah dilaporkan.
2. Buat issue baru dengan template **Laporan Bug** atau **Usulan Fitur**.
3. Untuk bug, sertakan:
   * langkah mengulang masalahnya;
   * hasil yang diharapkan dan yang terjadi;
   * versi aplikasi (lihat [Releases](https://github.com/wismoyo07/perpus_ci4/releases));
   * versi PHP dan MySQL;
   * screenshot atau isi log di `writable/logs/` bila ada.

> **Celah keamanan** jangan dilaporkan lewat issue publik. Kirim lewat [GitHub Security Advisory](https://github.com/wismoyo07/perpus_ci4/security/advisories/new) agar bisa diperbaiki dulu sebelum diumumkan.

## Menyiapkan Lingkungan Pengembangan

Ikuti [README bagian 5](README.md#5-cara-menjalankan-aplikasi-di-lokal):

```bash
git clone https://github.com/wismoyo07/perpus_ci4.git
cd perpus_ci4
composer install
cp env.example .env              # isi app.baseURL & koneksi database
php spark perpus:install --demo  # database contoh lengkap dengan akun demo
php spark serve
```

> **Pakai database khusus pengembangan.** `--demo` dan seeder bisa menambah atau mengganti data. Jangan arahkan `.env` ke database yang berisi data sekolah sungguhan.

## Mengirim Perubahan Kode (Pull Request)

1. *Fork* repositori, lalu buat branch dari `main` dengan nama yang menjelaskan isinya, misalnya `fix/denda-dibulatkan` atau `feature/import-excel`.
2. Ikuti gaya kode yang sudah ada:
   * PHP 8.2+;
   * pola CodeIgniter 4 (Controller → Service → Model);
   * komentar, pesan untuk pengguna, dan pesan validasi berbahasa Indonesia.
3. Perubahan struktur database **wajib lewat migrasi** di `app/Database/Migrations/`. Jangan mengubah migrasi yang sudah dirilis.
4. Semua keluaran ke HTML harus di-escape dengan `esc()`.
5. Tambahkan atau perbarui tes di `tests/unit/` untuk aturan bisnis yang berubah, lalu pastikan semua tes lulus:
   ```bash
   php vendor/bin/phpunit --no-coverage
   ```
6. Perbarui `README.md` bila ada perubahan cara pakai, aturan bisnis, atau cara instalasi.
7. Buka Pull Request ke `main` dan jelaskan **apa** yang berubah dan **mengapa**. Sertakan screenshot untuk perubahan tampilan. CI (GitHub Actions) akan menjalankan tes secara otomatis.

## Lisensi

Dengan mengirim kontribusi, Anda setuju kontribusi tersebut dilisensikan di bawah [Lisensi MIT](LICENSE) yang sama dengan proyek ini.
