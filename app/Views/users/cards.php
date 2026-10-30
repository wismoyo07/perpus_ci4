<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Anggota - <?= esc($setting['nama_lembaga']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 8mm;
        }
        body {
            background-color: #fff;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }
        .card-sheet {
            display: grid;
            grid-template-columns: repeat(2, 85.6mm);
            gap: 2mm 4mm;
            justify-content: center;
        }
        .member-card {
            width: 85.6mm;
            height: 54mm;
            border: 1px solid #333;
            border-radius: 3mm;
            padding: 3mm;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .member-card-header {
            display: flex;
            align-items: center;
            gap: 2mm;
            border-bottom: 1px solid #333;
            padding-bottom: 1.5mm;
            margin-bottom: 1.5mm;
        }
        .member-card-header img {
            height: 9mm;
            width: auto;
            object-fit: contain;
        }
        .member-card-header .lembaga {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.1;
        }
        .member-card-header .judul {
            font-size: 7pt;
            letter-spacing: 0.5px;
        }
        .member-card-body {
            font-size: 8pt;
            line-height: 1.35;
            flex: 1;
        }
        .member-card-body .nama {
            font-size: 10pt;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .member-card-barcode {
            text-align: center;
        }
        .member-card-barcode svg {
            max-width: 100%;
            height: 9mm;
        }
        .member-card-barcode .kode {
            font-size: 7pt;
            letter-spacing: 1px;
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
            .container-fluid {
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>

<div class="no-print-btn">
    <button onclick="window.print()" class="btn btn-primary shadow-lg"><i class="bi bi-printer"></i> Cetak Kartu</button>
</div>

<div class="container-fluid py-3">
    <?php if (empty($users)): ?>
        <p class="text-center text-muted">Tidak ada user yang dapat dicetak kartunya.</p>
    <?php else: ?>
        <div class="card-sheet">
            <?php foreach ($users as $u): ?>
                <div class="member-card">
                    <div class="member-card-header">
                        <?php if (!empty($setting['logo'])): ?>
                            <img src="<?= base_url('uploads/settings/' . $setting['logo']) ?>" alt="Logo">
                        <?php endif; ?>
                        <div>
                            <div class="lembaga"><?= esc($setting['nama_lembaga']) ?></div>
                            <div class="judul">KARTU ANGGOTA PERPUSTAKAAN</div>
                        </div>
                    </div>

                    <div class="member-card-body">
                        <div class="nama"><?= esc($u['name']) ?></div>
                        <div>NIS/NIP: <strong><?= esc($u['member_code']) ?></strong></div>
                        <div>
                            <?= esc(ucfirst($u['jenis_pengguna'] ?? '')) ?>
                            <?php if (!empty($u['class'])): ?>
                                &middot; Kelas <?= esc($u['class']) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($u['member_code'])): ?>
                        <div class="member-card-barcode">
                            <?= barcode_svg($u['member_code']) ?>
                            <div class="kode"><?= esc($u['member_code']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
