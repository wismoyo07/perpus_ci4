<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center py-5">
    <div class="col-lg-6 col-md-8 text-center">
        <div class="mb-3">
            <i class="fa-solid fa-lock text-danger" style="font-size: 64px;"></i>
        </div>
        <h2 class="text-dark fw-bold">Akses Ditolak</h2>
        <p class="text-muted mb-4"><?= esc($message) ?></p>
        <p class="text-muted small mb-4">
            Halaman ini tidak tersedia untuk peran
            <strong><?= esc(ucfirst((string) session('role'))) ?></strong>.
            Jika menurut Anda ini keliru, hubungi admin perpustakaan.
        </p>
        <a href="<?= site_url('dashboard') ?>" class="btn btn-primary">
            <i class="fa-solid fa-house me-1"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<?= $this->endSection() ?>
