<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Buku Hilang - <?= esc($setting['nama_lembaga']) ?></title>
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

<!-- Tombol Cetak Manual -->
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
        Laporan Pertanggungjawaban Buku Hilang & Ganti Rugi<br>
        <span style="font-size: 10pt; font-weight: normal; text-transform: none;">
            Periode Laporan: 
            <?php if (!empty($start_date) || !empty($end_date)): ?>
                <?= !empty($start_date) ? date('d/m/Y', strtotime($start_date)) : 'Awal' ?> s/d <?= !empty($end_date) ? date('d/m/Y', strtotime($end_date)) : 'Akhir' ?>
            <?php else: ?>
                Semua Periode
            <?php endif; ?>
            | Status: <?= !empty($status) ? esc(strtoupper($status)) : 'SEMUA STATUS' ?>
        </span>
    </div>

    <!-- I. RINGKASAN REKAPITULASI DUMMY -->
    <h5 class="fw-bold mb-2">I. Ringkasan Rekapitulasi Buku Hilang</h5>
    <table class="table table-bordered table-laporan align-middle mb-4">
        <thead>
            <tr>
                <th style="width: 8%;">No.</th>
                <th>Kategori Ganti Rugi</th>
                <th style="width: 30%;">Jumlah Kasus</th>
                <th style="width: 25%;">Keterangan Finansial</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Pending (Belum diselesaikan / Masih berstatus hutang)</td>
                <td class="text-center fw-bold text-danger"><?= number_format($stats['total_pending'] ?? 0) ?> Eksemplar</td>
                <td class="text-center">-</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Ganti Buku (Fisik buku diserahkan kembali)</td>
                <td class="text-center fw-bold"><?= number_format($stats['total_replaced'] ?? 0) ?> Eksemplar</td>
                <td class="text-center">Aset Fisik Terganti</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Ganti Uang (Membayar denda kehilangan tunai)</td>
                <td class="text-center fw-bold"><?= number_format($stats['total_paid'] ?? 0) ?> Eksemplar</td>
                <td class="text-center text-success fw-bold">Rp <?= number_format($stats['total_amount'] ?? 0, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Pemutihan (Dihapuskan secara resmi tanpa ganti rugi)</td>
                <td class="text-center fw-bold"><?= number_format($stats['total_written_off'] ?? 0) ?> Eksemplar</td>
                <td class="text-center">Dihapus dari Inventaris</td>
            </tr>
            <tr class="table-light fw-bold">
                <td colspan="2" class="text-end">Total Buku Hilang Terdata:</td>
                <td class="text-center"><?= number_format($stats['total_hilang'] ?? 0) ?> Eksemplar</td>
                <td class="text-center text-success">Rp <?= number_format($stats['total_amount'] ?? 0, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>

    <!-- II. RINCIAN DATA BUKU HILANG -->
    <h5 class="fw-bold mb-2">II. Rincian Pelacakan Buku Hilang</h5>
    <table class="table table-bordered table-laporan align-middle mb-5" style="font-size: 10pt;">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 10%;">Tgl Hilang</th>
                <th style="width: 13%;">Kode Trx</th>
                <th style="width: 15%;">Peminjam</th>
                <th>Judul Buku & Inv Asal</th>
                <th style="width: 12%;">Status Ganti</th>
                <th style="width: 10%;">Denda Uang</th>
                <th style="width: 20%;">Catatan Penyelesaian</th>
                <th style="width: 10%;">Verifikator</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($lostBooks)): ?>
                <tr>
                    <td colspan="9" class="text-center py-3">Tidak ada data buku hilang.</td>
                </tr>
            <?php else: ?>
                <?php $i = 1; foreach ($lostBooks as $l): ?>
                    <tr>
                        <td class="text-center"><?= $i++ ?></td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($l['tanggal_hilang'])) ?></td>
                        <td class="text-center fw-bold"><?= esc($l['kode_transaksi']) ?></td>
                        <td>
                            <?= esc(preg_replace('/\s*\(siswa\)|\s*\(guru\)|\s*\(pegawai\)/i', '', $l['member_name'])) ?><br>
                            <small class="text-muted" style="font-size: 8pt; text-transform: uppercase;">(<?= esc($l['jenis_pengguna']) ?>)</small>
                        </td>
                        <td>
                            <strong><?= esc($l['judul']) ?></strong><br>
                            <small class="text-muted" style="font-size: 8pt;">Inv: <?= esc($l['original_kode_inventaris'] ?: '-') ?></small>
                        </td>
                        <td class="text-center">
                            <?php if($l['lost_settlement_status'] === 'replaced'): ?>
                                Ganti Buku
                            <?php elseif($l['lost_settlement_status'] === 'paid'): ?>
                                Ganti Uang
                            <?php elseif($l['lost_settlement_status'] === 'written_off'): ?>
                                Pemutihan
                            <?php else: ?>
                                PENDING
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?= $l['lost_settlement_status'] === 'paid' ? 'Rp '.number_format($l['lost_settlement_amount'], 0, ',', '.') : '-' ?>
                        </td>
                        <td><?= esc($l['lost_note'] ?: '-') ?></td>
                        <td class="text-center">
                            <?= esc($l['verifier_name'] ?: '-') ?><br>
                            <small class="text-muted" style="font-size: 7.5pt;"><?= $l['settled_at'] ? date('d/m/y', strtotime($l['settled_at'])) : '' ?></small>
                        </td>
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
                    Pustakawan Perpustakaan,
                </div>
                <div>
                    <br><br><br>
                    <strong><u><?= esc(session()->get('name') ?: 'Petugas Perpustakaan') ?></u></strong><br>
                    NIP / Kode Staff: <?= esc(session()->get('username') ?: '-') ?>
                </div>
            </div>
        </div>

        <!-- Tanda Tangan Kepala Lembaga -->
        <div class="col-6">
            <div class="signature-box text-center">
                <div>
                    Mengetahui,<br>
                    Kepala Sekolah / Pimpinan Lembaga,
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
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            window.print();
        }, 500);
    });
</script>
</body>
</html>
