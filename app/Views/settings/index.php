<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fa-solid fa-gear text-primary"></i> Pengaturan Identitas Sekolah</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="fa-solid fa-triangle-exclamation me-2"></i> Perhatian!</h5>
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title mb-0 text-secondary fw-semibold">Detail Profil Lembaga</h5>
            </div>
            <div class="card-body">
                <?= $this->include('settings/_form') ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Preview logo secara instan sebelum diupload
    document.getElementById('logo-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logo-preview').setAttribute('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });
</script>

<?= $this->endSection() ?>
