<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-4" style="max-width: 520px;">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
            <h4 class="fw-bold mb-1"><i class="fa-solid fa-key text-warning me-2"></i> Ganti Password</h4>
            <p class="text-muted">
                Halo, <b><?= esc(session('name')) ?></b>. Demi keamanan akun, Anda wajib mengganti password sebelum
                melanjutkan (akun baru, password direset petugas, atau akun demo).
            </p>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        <?php foreach ((array) session()->getFlashdata('errors') as $e): ?>
                            <li><?= esc($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="/force-change-password">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="newPassword">Password Baru</label>
                    <input type="password" name="password" id="newPassword" class="form-control"
                           required minlength="8" maxlength="64" autocomplete="new-password">
                    <?= $this->include('auth/_password_hints') ?>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="confirmPassword">Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" id="confirmPassword" class="form-control"
                           required maxlength="64" autocomplete="new-password">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/logout" class="btn btn-outline-secondary">Keluar</a>
                    <button class="btn btn-primary fw-bold">Simpan Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
