<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h4 class="text-dark fw-bold"><i class="fa-solid fa-user-plus text-primary me-2"></i> Tambah User</h4>
            <p class="text-muted mb-0">Buat akun anggota baru (siswa, guru, atau pegawai).</p>
        </div>
        <a href="/users" class="btn btn-outline-secondary shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-3 overflow-hidden" style="max-width: 640px;">
        <div class="card-body p-4">
            <?= $this->include('users/_create_form') ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
