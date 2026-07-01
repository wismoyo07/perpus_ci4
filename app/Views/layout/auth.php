<?php
$db = \Config\Database::connect();
$setting = $db->table('app_settings')->where('id', 1)->get()->getRowArray();
$logoUrl = ($setting && $setting['logo']) ? base_url('uploads/settings/' . $setting['logo']) : null;
$appName = ($setting && $setting['nama_lembaga']) ? $setting['nama_lembaga'] : 'Perpustakaan';
$address = ($setting && $setting['alamat']) ? $setting['alamat'] : '';
?>
<!doctype html>
<html lang="en" data-page-module="login-register">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | <?= esc($appName) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= base_url('assets/style.css'); ?>">
    

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="<?=  base_url('assets/src/css/widgets-consistent.css'); ?>">

    <meta name="description" content="Login and registration - <?= esc($appName) ?>">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome and all other CSS imported via Vite -->
</head>

<body>
    <div class="nk-auth-wrapper">
        <!-- Left branding panel -->
        <div class="nk-auth-brand">
            <div class="nk-auth-brand-inner">
                <div class="nk-auth-logo mb-3">
                    <?php if ($logoUrl): ?>
                        <img src="<?= $logoUrl ?>" alt="Logo" style="max-height: 80px; width: auto; object-fit: contain;">
                    <?php else: ?>
                        <i class="fa-solid fa-book-open text-white" style="font-size: 60px;"></i>
                    <?php endif; ?>
                </div>
                <h1 class="text-white mt-3"><?= esc($appName) ?></h1>
                <?php if ($address): ?>
                    <p class="text-white-50"><i class="fa-solid fa-location-dot me-1"></i> <?= esc($address) ?></p>
                <?php else: ?>
                    <p class="text-white-50">Sistem Informasi Perpustakaan Digital</p>
                <?php endif; ?>
                <div class="nk-auth-features">
                    <div class="nk-auth-feature">
                        <i class="fa-solid fa-shield"></i>
                        <span>Secure Authentication</span>
                    </div>
                    <div class="nk-auth-feature">
                        <i class="fa-solid fa-book"></i>
                        <span>Katalog Buku Digital</span>
                    </div>
                    <div class="nk-auth-feature">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Informasi Sirkulasi</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right form panel -->
        <div class="nk-auth-form-panel">
            <?=  $this->renderSection('content') ?>
        </div>
    </div>

    <!-- ONLY VITE ENTRY POINT - Everything else bundled by Vite -->
    <script type="module" src="<?=  base_url('assets/src/js/pages/login-register.js'); ?>"></script>
</body>

</html>
