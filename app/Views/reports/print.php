<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perpustakaan - <?= esc($setting['nama_lembaga']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff;
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            color: #000;
        }
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .school-logo {
            max-height: 90px;
            width: auto;
            object-fit: contain;
        }
        .kop-text h2 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-text h3 {
            font-size: 14pt;
            font-weight: bold;
            margin: 5px 0 0 0;
            text-transform: uppercase;
        }
        .kop-text p {
            font-size: 10pt;
            margin: 5px 0 0 0;
            line-height: 1.2;
        }
        .doc-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 25px;
        }
        .table-laporan {
            border-color: #000 !important;
        }
        .table-laporan th {
            background-color: #f2f2f2 !important;
            color: #000 !important;
            font-weight: bold;
            text-align: center;
            border-color: #000 !important;
        }
        .table-laporan td {
            border-color: #000 !important;
        }
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .no-print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        @media print {
            .no-print-btn {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
            @page {
                size: A4;
                margin: 2cm;
            }
        }
    </style>
</head>
<body>

<!-- Tombol Cetak Manual (Hanya melayang di browser) -->
<div class="no-print-btn">
    <button onclick="window.print()" class="btn btn-primary shadow-lg"><i class="bi bi-printer"></i> Cetak Dokumen</button>
    <button onclick="window.close()" class="btn btn-secondary shadow-lg">Tutup</button>
</div>

<div class="container my-3">
    <!-- Kop Surat Resmi -->
    <div class="kop-surat d-flex align-items-center justify-content-between">
        <div class="pe-3">
            <img class="school-logo" src="<?= $setting['logo'] ? base_url('uploads/settings/' . $setting['logo']) : 'https://placehold.co/120x120?text=Logo+Sekolah' ?>" alt="Logo Sekolah">
        </div>
        <div class="kop-text text-center flex-grow-1">
            <h2>Pemerintah Daerah Kabupaten/Kota</h2>
            <h3><?= esc($setting['nama_lembaga']) ?></h3>
            <p>
                <?= esc($setting['alamat']) ?><br>
                Status Akreditasi: <strong><?= esc($setting['status_akreditasi'] ?: '-') ?></strong> | NPSN/Kode: <strong><?= esc($setting['kode_lembaga'] ?: '-') ?></strong><br>
                Kontak/Website: <?= esc($setting['website_url'] ?: '-') ?>
            </p>
        </div>
        <div class="ps-3" style="width: 100px;"></div> <!-- Spacer balancing -->
    </div>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        Laporan Inventarisasi & Sirkulasi Perpustakaan<br>
        <span style="font-size: 10pt; font-weight: normal; text-transform: none;">Per tanggal cetak: <?= esc($tanggal_cetak) ?></span>
    </div>

    <!-- 1. Ringkasan Inventaris Buku -->
    <h5 class="fw-bold mb-2">I. Ringkasan Inventarisasi Buku</h5>
    <table class="table table-bordered table-laporan align-middle mb-4">
        <thead>
            <tr>
                <th style="width: 8%;">No.</th>
                <th>Kategori Statistik</th>
                <th style="width: 25%;">Jumlah / Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Jumlah Judul Buku terdaftar</td>
                <td class="text-center fw-bold"><?= $total_titles ?> Judul</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Jumlah Seluruh Buku Fisik (Stok Total)</td>
                <td class="text-center fw-bold"><?= $total_books ?> Eksemplar</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Jumlah Buku Tersedia di Perpustakaan (Stok Ready)</td>
                <td class="text-center fw-bold text-success"><?= $available_books ?> Eksemplar</td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Jumlah Kategori Buku terdaftar (Klasifikasi DDC)</td>
                <td class="text-center"><?= $total_categories ?> Kelas, <?= (int) $total_ddc_detail ?> Divisi/Seksi</td>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td>Jumlah Buku Hilang (Dalam proses penyelesaian)</td>
                <td class="text-center text-danger fw-bold"><?= $lost_books ?> Eksemplar</td>
            </tr>
        </tbody>
    </table>

    <p class="fw-bold mb-2">Sebaran Koleksi per Kelas DDC</p>
    <table class="table table-bordered table-laporan align-middle mb-4">
        <thead>
            <tr>
                <th style="width: 12%;">Kelas</th>
                <th>Deskripsi</th>
                <th style="width: 18%;">Judul</th>
                <th style="width: 18%;">Eksemplar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ddc_distribution as $d): ?>
                <tr>
                    <td class="text-center"><?= esc($d['code_ddc']) ?></td>
                    <td><?= esc($d['desc_ddc']) ?></td>
                    <td class="text-center"><?= (int) $d['judul'] ?></td>
                    <td class="text-center"><?= (int) $d['eksemplar'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p class="fw-bold mb-2">Rincian Koleksi per Divisi / Seksi DDC</p>
    <table class="table table-bordered table-laporan align-middle mb-4">
        <thead>
            <tr>
                <th width="90">Kode</th>
                <th>Klasifikasi</th>
                <th class="text-center" width="120">Judul</th>
                <th class="text-center" width="120">Eksemplar</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ddc_detail)): ?>
                <tr><td colspan="4" class="text-center text-muted">Belum ada buku yang diklasifikasi.</td></tr>
            <?php endif; ?>
            <?php foreach ($ddc_detail as $d): ?>
                <tr>
                    <td class="text-center"><?= esc($d['code_ddc']) ?></td>
                    <td><?= esc($d['desc_ddc']) ?></td>
                    <td class="text-center"><?= (int) $d['judul'] ?></td>
                    <td class="text-center"><?= (int) $d['eksemplar'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- 2. Ringkasan Transaksi Peminjaman -->
    <h5 class="fw-bold mb-2">II. Status Peminjaman Aktif</h5>
    <table class="table table-bordered table-laporan align-middle mb-4">
        <thead>
            <tr>
                <th style="width: 8%;">No.</th>
                <th>Jenis Peminjaman</th>
                <th>Keterangan Durasi</th>
                <th style="width: 25%;">Buku Sedang Dipinjam</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Peminjaman Jangka Pendek</td>
                <td>Durasi pinjam &le; 30 Hari (Buku Bacaan/Referensi)</td>
                <td class="text-center fw-bold"><?= $short_term ?> Eksemplar</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Peminjaman Jangka Panjang</td>
                <td>Durasi pinjam > 30 Hari (Buku Pelajaran Sekolah)</td>
                <td class="text-center fw-bold"><?= $long_term ?> Eksemplar</td>
            </tr>
            <tr class="table-light fw-bold">
                <td colspan="3" class="text-end">Total Buku Sedang Dipinjam:</td>
                <td class="text-center"><?= $borrowed_books ?> Eksemplar</td>
            </tr>
        </tbody>
    </table>

    <!-- 3. Tabel Riwayat Lengkap Transaksi -->
    <h5 class="fw-bold mb-2">III. Log Riwayat Sirkulasi Transaksi</h5>
    <table class="table table-bordered table-laporan align-middle mb-5">
        <thead>
            <tr>
                <th style="width: 15%;">Kode Transaksi</th>
                <th>Nama Anggota</th>
                <th style="width: 15%;">Tgl Pinjam</th>
                <th style="width: 15%;">Jatuh Tempo</th>
                <th style="width: 15%;">Tgl Kembali</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 15%;">Denda</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="7" class="text-center py-3">Tidak ada riwayat transaksi sirkulasi.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($transactions as $t): ?>
                    <tr>
                        <td class="text-center fw-bold"><?= esc($t['kode_transaksi']) ?></td>
                        <td><?= esc($t['member_name']) ?></td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($t['tanggal_pinjam'])) ?></td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($t['jatuh_tempo'])) ?></td>
                        <td class="text-center"><?= $t['tanggal_kembali'] ? date('d/m/Y', strtotime($t['tanggal_kembali'])) : '-' ?></td>
                        <td class="text-center">
                            <?php if ($t['status'] === 'dipinjam'): ?>
                                Dipinjam
                            <?php elseif ($t['status'] === 'terlambat'): ?>
                                Terlambat
                            <?php else: ?>
                                Selesai
                            <?php endif; ?>
                        </td>
                        <td class="text-end">Rp <?= number_format($t['total_denda'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Area Tanda Tangan -->
    <div class="row signature-section">
        <!-- Tanda Tangan Petugas -->
        <div class="col-6">
            <div class="signature-box">
                <div>
                    Petugas Perpustakaan,
                </div>
                <div>
                    <br><br><br>
                    <strong><u><?= esc(session()->get('name') ?: 'Petugas Perpustakaan') ?></u></strong><br>
                    NIP / Kode Staff: <?= esc(session()->get('username') ?: '-') ?>
                </div>
            </div>
        </div>

        <!-- Tanda Tangan Kepala Sekolah -->
        <div class="col-6">
            <div class="signature-box text-center">
                <div>
                    Mengetahui,<br>
                    Kepala Sekolah <?= esc($setting['nama_lembaga']) ?>,
                </div>
                <div>
                    <br><br><br>
                    <strong><u><?= esc($setting['kepala_sekolah'] ?: '..........................................') ?></u></strong><br>
                    NIP: ..........................................
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Pemicu print dialog secara otomatis saat halaman selesai dimuat
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            window.print();
        }, 500);
    });
</script>
</body>
</html>
