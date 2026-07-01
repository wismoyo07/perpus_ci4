<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Perpustakaan') ?></title>

    <!-- Core Bootstrap 5 and Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Notika Template CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/style.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/header-modern-clean.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/navbar-stable.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/widgets-consistent.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/responsive.css'); ?>">
    
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f6f8fa;
        }
        /* Custom tweaks to keep page layout aligned with Notika */
        .notika-content-wrapper {
            padding: 30px 0;
        }
        /* Custom Pager Styling to Match Notika Theme */
        .pagination {
            margin-top: 20px;
            justify-content: center;
        }
        .pagination .page-item .page-link {
            color: #666;
            border: 1px solid #e9ecef;
            padding: 8px 16px;
            transition: all 0.2s ease;
        }
        .pagination .page-item .page-link:hover {
            background-color: rgba(0, 194, 146, 0.05);
            color: #00c292;
            border-color: #00c292;
        }
        .pagination .page-item.active .page-link {
            background-color: #00c292;
            border-color: #00c292;
            color: #fff;
            font-weight: 600;
        }
        .pagination .page-item.disabled .page-link {
            color: #ccc;
            background-color: #fcfcfc;
            border-color: #e9ecef;
        }
    </style>
</head>
<body>

<?php if (!session()->get('force_password_change')): ?>
    <?= $this->include('layout/header') ?>
    <?= $this->include('layout/navbar') ?>
<?php endif; ?>

<div class="container notika-content-wrapper">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>

<!-- Core JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>