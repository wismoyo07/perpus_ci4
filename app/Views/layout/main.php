<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'Perpustakaan' ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<?php if (!session()->get('force_password_change')): ?>
    <?= $this->include('layout/navbar') ?>
<?php endif; ?>

<div class="container-fluid">
    <div class="row">
            <?php if (!session()->get('force_password_change')): ?>
                <?= $this->include('layout/sidebar') ?>
            <?php endif; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
            <?= $this->renderSection('content') ?>
        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>