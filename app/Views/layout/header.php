<?php
$db = \Config\Database::connect();
$setting = $db->table('app_settings')->where('id', 1)->get()->getRowArray();
$logoUrl = ($setting && $setting['logo']) ? base_url('uploads/settings/' . $setting['logo']) : null;
$appName = ($setting && $setting['nama_lembaga']) ? $setting['nama_lembaga'] : 'Perpustakaan';
?>
<header class="notika-header bg-white py-3 border-bottom shadow-sm">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="notika-brand d-flex align-items-center">
                    <a href="/<?= session()->get('role') ?>/dashboard" class="d-flex align-items-center text-decoration-none">
                        <?php if ($logoUrl): ?>
                            <img src="<?= $logoUrl ?>" alt="Logo" class="me-2" style="max-height: 40px; width: auto; object-fit: contain;">
                        <?php else: ?>
                            <i class="fa-solid fa-book-open text-success me-2" style="font-size: 28px;"></i>
                        <?php endif; ?>
                        <span class="fs-4 fw-bold text-success"><?= esc($appName) ?></span>
                    </a>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 text-md-end text-sm-start mt-md-0 mt-2">
                <div class="d-flex justify-content-md-end justify-content-start align-items-center gap-3">
                    <span class="text-muted d-none d-sm-inline">Selamat Datang,</span>
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-circle-user text-success"></i>
                            <strong><?= esc(session()->get('name')) ?></strong>
                            <span class="badge bg-success-subtle text-success"><?= esc(ucfirst(session()->get('role'))) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item" href="/profile"><i class="fa-solid fa-id-card me-2 text-muted"></i> Profil Saya</a></li>
                            <li><a class="dropdown-item" href="/profile#password"><i class="fa-solid fa-key me-2 text-muted"></i> Ganti Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/logout"><i class="fa-solid fa-right-from-bracket me-2"></i> Keluar</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
