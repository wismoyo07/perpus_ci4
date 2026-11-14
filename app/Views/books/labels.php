<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Buku - <?= esc($book['judul']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 8mm;
        }
        body {
            background-color: #fff;
            font-family: 'Roboto', Arial, sans-serif;
            color: #000;
        }
        .label-sheet {
            display: grid;
            grid-template-columns: repeat(3, 62mm);
            gap: 2mm 3mm;
            justify-content: center;
        }
        .book-label {
            width: 62mm;
            height: 32mm;
            border: 1px dashed #666;
            padding: 2mm;
            display: flex;
            align-items: center;
            gap: 2mm;
            overflow: hidden;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .book-label .qr svg {
            width: 26mm;
            height: 26mm;
            display: block;
        }
        .book-label .info {
            flex: 1;
            min-width: 0;
            line-height: 1.2;
        }
        .book-label .lembaga {
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .book-label .judul {
            font-size: 7.5pt;
            margin: 1mm 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .book-label .panggil {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 0.5mm;
        }
        .book-label .kode {
            font-size: 7pt;
            font-weight: bold;
            word-break: break-word;
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
    <button onclick="window.print()" class="btn btn-primary shadow-lg"><i class="fa-solid fa-print"></i> Cetak Label</button>
</div>

<div class="container-fluid py-3">
    <?php if (empty($items)): ?>
        <p class="text-center text-muted">Belum ada salinan buku yang dapat dicetak labelnya.</p>
    <?php else: ?>
        <div class="label-sheet">
            <?php foreach ($items as $item): ?>
                <div class="book-label">
                    <div class="qr"><?= qrcode_svg($item['kode_inventaris']) ?></div>
                    <div class="info">
                        <div class="lembaga"><?= esc($setting['nama_lembaga']) ?></div>
                        <div class="judul"><?= esc($book['judul']) ?></div>
                        <?php if (!empty($book['nomor_panggil'])): ?>
                            <div class="panggil"><?= esc($book['nomor_panggil']) ?></div>
                        <?php endif; ?>
                        <div class="kode"><?= esc($item['kode_inventaris']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
