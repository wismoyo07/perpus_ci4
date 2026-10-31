<?php
helper('terbilang');
$rp = static fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
$bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tgl = static fn ($d) => (int) date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi <?= esc($payment['nomor_kwitansi']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        @page {
            size: A5;
            margin: 8mm;
        }
        body {
            background-color: #f1f1f1;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            font-size: 9pt;
        }
        .kwitansi {
            position: relative;
            width: 132mm;
            min-height: 180mm;
            margin: 10mm auto;
            padding: 6mm;
            background: #fff;
            border: 1px solid #333;
        }
        .kop {
            display: flex;
            align-items: center;
            gap: 3mm;
            border-bottom: 2px solid #000;
            padding-bottom: 2mm;
            margin-bottom: 3mm;
        }
        .kop img {
            height: 14mm;
            width: auto;
            object-fit: contain;
        }
        .kop .lembaga {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.15;
        }
        .judul {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 0;
        }
        .nomor {
            text-align: center;
            margin-bottom: 3mm;
        }
        .data td {
            padding: 0.6mm 1mm;
            vertical-align: top;
        }
        .terbilang {
            border: 1px dashed #555;
            padding: 1.5mm 2mm;
            font-style: italic;
        }
        .rincian {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        .rincian th, .rincian td {
            border: 1px solid #444;
            padding: 1mm 1.5mm;
            vertical-align: top;
        }
        .rincian th {
            background: #eee;
            text-align: center;
        }
        .ttd {
            display: flex;
            justify-content: space-between;
            margin-top: 5mm;
            text-align: center;
        }
        .ttd > div {
            width: 45%;
        }
        .ttd .ruang {
            height: 16mm;
        }
        .salinan {
            position: absolute;
            top: 70mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 40pt;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.08);
            transform: rotate(-25deg);
            pointer-events: none;
        }
        .no-print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        @media print {
            body {
                background: #fff;
            }
            .no-print-btn {
                display: none;
            }
            .kwitansi {
                margin: 0;
                border: none;
                width: auto;
                min-height: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>

<div class="no-print-btn d-flex gap-2">
    <a href="<?= esc($backUrl) ?>" class="btn btn-outline-secondary shadow-lg"><i class="bi bi-arrow-left"></i> Kembali</a>
    <button onclick="window.print()" class="btn btn-primary shadow-lg"><i class="bi bi-printer"></i> Cetak Kwitansi</button>
</div>

<div class="kwitansi">
    <?php if ($salinan): ?>
        <div class="salinan">SALINAN</div>
    <?php endif; ?>

    <div class="kop">
        <?php if (!empty($setting['logo'])): ?>
            <img src="<?= base_url('uploads/settings/' . $setting['logo']) ?>" alt="Logo">
        <?php endif; ?>
        <div>
            <div class="lembaga"><?= esc($setting['nama_lembaga'] ?? 'Perpustakaan Sekolah') ?></div>
            <div>Perpustakaan Sekolah</div>
            <?php if (!empty($setting['alamat'])): ?>
                <div class="small"><?= esc($setting['alamat']) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <p class="judul">KWITANSI PEMBAYARAN DENDA</p>
    <div class="nomor">
        No. <b><?= esc($payment['nomor_kwitansi']) ?></b>
        <?php if ($salinan): ?><span class="ms-1">(salinan)</span><?php endif; ?>
    </div>

    <table class="data mb-2">
        <tr>
            <td style="width: 32mm;">Telah terima dari</td>
            <td>: <b><?= esc($payment['member_name']) ?></b></td>
        </tr>
        <tr>
            <td>NIS / NIP</td>
            <td>: <?= esc($payment['member_code'] ?: '-') ?></td>
        </tr>
        <tr>
            <td><?= $payment['jenis_pengguna'] === 'siswa' ? 'Kelas' : 'Status' ?></td>
            <td>: <?= esc($payment['jenis_pengguna'] === 'siswa' ? ($payment['class'] ?: '-') : ucfirst($payment['jenis_pengguna'])) ?></td>
        </tr>
        <tr>
            <td>Uang sejumlah</td>
            <td>: <b style="font-size: 11pt;"><?= $rp($payment['total']) ?></b></td>
        </tr>
    </table>

    <div class="terbilang mb-2">
        Terbilang: <b><?= ucfirst(terbilang((int) round((float) $payment['total']))) ?> rupiah</b>
    </div>

    <div class="mb-1">Untuk pembayaran denda perpustakaan dengan rincian:</div>
    <table class="rincian mb-2">
        <thead>
            <tr>
                <th style="width: 6mm;">No</th>
                <th>Buku</th>
                <th style="width: 34mm;">Keterangan</th>
                <th style="width: 20mm;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payment['items'] as $i => $item): ?>
                <tr>
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td>
                        <?php if ($item['transaction_detail_id']): ?>
                            <?= esc($item['judul']) ?>
                            <div class="text-muted"><?= esc($item['kode_inventaris'] ?? '') ?> · <?= esc($item['kode_transaksi']) ?></div>
                        <?php else: ?>
                            Denda lama (per transaksi)
                            <div class="text-muted"><?= esc($item['kode_transaksi']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($item['transaction_detail_id']): ?>
                            <?= $item['is_lost'] ? 'Buku hilang' : 'Terlambat' ?>
                            <?= (int) $item['hari_dikenakan'] ?> hari × <?= $rp($item['tarif']) ?>
                            <?php if ((float) $item['jumlah'] !== (float) $item['jumlah_awal']): ?>
                                <div class="text-muted">disesuaikan dari <?= $rp($item['jumlah_awal']) ?></div>
                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="text-end"><?= $rp($item['jumlah']) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" class="text-end fw-bold">TOTAL</td>
                <td class="text-end fw-bold"><?= $rp($payment['total']) ?></td>
            </tr>
        </tbody>
    </table>

    <?php if (!empty($payment['catatan'])): ?>
        <div class="mb-2">Catatan: <i><?= esc($payment['catatan']) ?></i></div>
    <?php endif; ?>

    <div class="text-end">Dibayar pada <?= $tgl($payment['dibayar_at']) ?>, pukul <?= date('H:i', strtotime($payment['dibayar_at'])) ?></div>

    <div class="ttd">
        <div>
            Penyetor,
            <div class="ruang"></div>
            <b>( ........................................ )</b>
        </div>
        <div>
            Petugas Penerima,
            <div class="ruang"></div>
            <b><u><?= esc($payment['petugas_name']) ?></u></b>
        </div>
    </div>

    <div class="mt-3 text-muted" style="font-size: 7pt;">
        Kwitansi ini sah sebagai bukti pembayaran denda perpustakaan. Dicetak <?= date('d/m/Y H:i') ?>.
    </div>
</div>

</body>
</html>
