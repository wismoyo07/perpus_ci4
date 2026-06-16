<?php
$db = \Config\Database::connect();
$setting = $db->table('app_settings')->where('id', 1)->get()->getRowArray();
$logoUrl = ($setting && $setting['logo']) ? base_url('uploads/settings/' . $setting['logo']) : null;
$appName = ($setting && $setting['nama_lembaga']) ? $setting['nama_lembaga'] : 'Perpustakaan System';
?>
<nav class="navbar navbar-dark bg-dark sticky-top shadow">
    <div class="container-fluid">
        <span class="navbar-brand d-flex align-items-center">
            <?php if ($logoUrl): ?>
                <img src="<?= $logoUrl ?>" alt="Logo" class="me-2" style="max-height: 30px; width: auto; object-fit: contain;">
            <?php else: ?>
                <i class="bi bi-book-half me-2"></i>
            <?php endif; ?>
            <?= esc($appName) ?>
        </span>

        
        <div class="text-white">
            <?= session()->get('name') ?> |
            <a href="/logout" class="text-white text-decoration-none">Logout</a>
        </div>
    </div>
</nav>