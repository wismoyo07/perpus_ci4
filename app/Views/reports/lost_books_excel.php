<?php
// Mencegah format angka ilmiah atau string aneh di Excel
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        .title {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
        }
        .subtitle {
            font-size: 11pt;
            text-align: center;
        }
        .header-table th {
            background-color: #d9e1f2;
            color: #000;
            font-weight: bold;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }
        .data-table td {
            border: 1px solid #000;
            vertical-align: top;
        }
        .number-cell {
            text-align: center;
        }
        .currency-cell {
            text-align: right;
            mso-number-format: "\#\,\#\#0"; /* Excel formatting */
        }
        .stats-table td {
            border: 1px solid #000;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Judul Laporan -->
    <table>
        <tr>
            <td colspan="14" class="title">LAPORAN PERTANGGUNGJAWABAN BUKU HILANG & GANTI RUGI</td>
        </tr>
        <tr>
            <td colspan="14" class="subtitle">Perpustakaan: <?= esc($setting['nama_lembaga']) ?></td>
        </tr>
        <tr>
            <td colspan="14" class="subtitle">
                Periode Laporan: 
                <?php if (!empty($start_date) || !empty($end_date)): ?>
                    <?= !empty($start_date) ? date('d/m/Y', strtotime($start_date)) : 'Awal' ?> s/d <?= !empty($end_date) ? date('d/m/Y', strtotime($end_date)) : 'Akhir' ?>
                <?php else: ?>
                    Semua Periode
                <?php endif; ?>
                | Status: <?= !empty($status) ? esc(strtoupper($status)) : 'SEMUA STATUS' ?>
            </td>
        </tr>
        <tr>
            <td colspan="14">Tanggal Ekspor: <?= esc($tanggal_cetak) ?></td>
        </tr>
        <tr>
            <td colspan="14">Petugas Ekspor: <?= esc(session()->get('name') ?: 'Staff') ?></td>
        </tr>
    </table>

    <br>

    <!-- Ringkasan Rekapitulasi Statistik -->
    <table>
        <tr>
            <td colspan="4" style="background-color: #f2f2f2; font-weight: bold; border: 1px solid #000;">RINGKASAN STATISTIK</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000;">Total Kasus Buku Hilang</td>
            <td style="border: 1px solid #000; font-weight: bold; text-align: center;"><?= number_format($stats['total_hilang'] ?? 0) ?></td>
            <td style="border: 1px solid #000;" colspan="2">Eksemplar</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000;">Belum Diselesaikan (Pending)</td>
            <td style="border: 1px solid #000; font-weight: bold; text-align: center; color: #ff0000;"><?= number_format($stats['total_pending'] ?? 0) ?></td>
            <td style="border: 1px solid #000;" colspan="2">Eksemplar</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000;">Diselesaikan dg Ganti Buku Fisik</td>
            <td style="border: 1px solid #000; font-weight: bold; text-align: center;"><?= number_format($stats['total_replaced'] ?? 0) ?></td>
            <td style="border: 1px solid #000;" colspan="2">Eksemplar</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000;">Diselesaikan dg Ganti Uang (Denda)</td>
            <td style="border: 1px solid #000; font-weight: bold; text-align: center;"><?= number_format($stats['total_paid'] ?? 0) ?></td>
            <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #e2efda;">Rp <?= number_format($stats['total_amount'] ?? 0, 0, ',', '.') ?></td>
            <td style="border: 1px solid #000; font-style: italic;">Kas Masuk Diterima</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000;">Diselesaikan dg Pemutihan (Hapus Aset)</td>
            <td style="border: 1px solid #000; font-weight: bold; text-align: center;"><?= number_format($stats['total_written_off'] ?? 0) ?></td>
            <td style="border: 1px solid #000;" colspan="2">Eksemplar</td>
        </tr>
    </table>

    <br>

    <!-- Tabel Rincian Data Buku Hilang -->
    <table class="data-table">
        <thead>
            <tr class="header-table">
                <th style="width: 50px;">No</th>
                <th style="width: 100px;">Tanggal Hilang</th>
                <th style="width: 150px;">Kode Transaksi</th>
                <th style="width: 180px;">Nama Peminjam</th>
                <th style="width: 100px;">Tipe Pengguna</th>
                <th style="width: 100px;">Kode Katalog</th>
                <th style="width: 250px;">Judul Buku</th>
                <th style="width: 150px;">Kode Inventaris Asal</th>
                <th style="width: 120px;">Status Ganti Rugi</th>
                <th style="width: 120px;">Denda Uang (Rp)</th>
                <th style="width: 300px;">Catatan Penyelesaian Aplikasi</th>
                <th style="width: 150px;">Verifikator (Staff)</th>
                <th style="width: 120px;">Tanggal Settle</th>
                <!-- KOLOM TAMBAHAN KUSTOM UNTUK AUDIT MANUAL (FLEKSIBILITAS) -->
                <th style="width: 250px; background-color: #fff2cc; border: 2px solid #ffc000; font-weight: bold;">Catatan Manual Petugas / Lembaga (Kolom Tambahan)</th>
                <th style="width: 200px; background-color: #fff2cc; border: 2px solid #ffc000; font-weight: bold;">Paraf / No. Bukti Kas Fisik (Kolom Tambahan)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($lostBooks)): ?>
                <tr>
                    <td colspan="15" style="text-align: center; border: 1px solid #000;">Tidak ada data buku hilang.</td>
                </tr>
            <?php else: ?>
                <?php $i = 1; foreach ($lostBooks as $l): ?>
                    <tr>
                        <td class="number-cell" style="border: 1px solid #000;"><?= $i++ ?></td>
                        <td class="number-cell" style="border: 1px solid #000;"><?= date('d/m/Y', strtotime($l['tanggal_hilang'])) ?></td>
                        <td class="number-cell" style="border: 1px solid #000; font-weight: bold; mso-number-format: '\@';"><?= esc($l['kode_transaksi']) ?></td>
                        <td style="border: 1px solid #000;"><?= esc(preg_replace('/\s*\(siswa\)|\s*\(guru\)|\s*\(pegawai\)/i', '', $l['member_name'])) ?></td>
                        <td class="number-cell" style="border: 1px solid #000; text-transform: uppercase;"><?= esc($l['jenis_pengguna']) ?></td>
                        <td class="number-cell" style="border: 1px solid #000; mso-number-format: '\@';"><?= esc($l['kode_buku']) ?></td>
                        <td style="border: 1px solid #000;"><?= esc($l['judul']) ?></td>
                        <td class="number-cell" style="border: 1px solid #000; mso-number-format: '\@';"><?= esc($l['original_kode_inventaris'] ?: '-') ?></td>
                        <td class="number-cell" style="border: 1px solid #000; font-weight: bold;">
                            <?php if($l['lost_settlement_status'] === 'replaced'): ?>
                                Replaced (Ganti Buku)
                            <?php elseif($l['lost_settlement_status'] === 'paid'): ?>
                                Paid (Ganti Uang)
                            <?php elseif($l['lost_settlement_status'] === 'written_off'): ?>
                                Written Off (Pemutihan)
                            <?php elseif($l['lost_settlement_status'] === 'found'): ?>
                                Found (Ditemukan)
                            <?php else: ?>
                                PENDING
                            <?php endif; ?>
                        </td>
                        <td class="currency-cell" style="border: 1px solid #000;"><?= $l['lost_settlement_status'] === 'paid' ? intval($l['lost_settlement_amount']) : 0 ?></td>
                        <td style="border: 1px solid #000;"><?= esc($l['lost_note'] ?: '-') ?></td>
                        <td style="border: 1px solid #000;"><?= esc($l['verifier_name'] ?: '-') ?></td>
                        <td class="number-cell" style="border: 1px solid #000;"><?= $l['settled_at'] ? date('d/m/Y H:i', strtotime($l['settled_at'])) : '-' ?></td>
                        <!-- Kolom tambahan kosong sengaja dibuat untuk keperluan manual penulisan tangan/excel -->
                        <td style="border: 2px dashed #ffc000; background-color: #fafdf6;"></td>
                        <td style="border: 2px dashed #ffc000; background-color: #fafdf6;"></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
