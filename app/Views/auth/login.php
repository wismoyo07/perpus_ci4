<?= $this->extend('layout/auth') ?>
<?= $this->section('content') ?>

<div class="card shadow auth-card">
    <div class="card-body p-4">

        <h4 class="text-center mb-4">Login</h4>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/auth/login" class="needs-validation" novalidate>
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="identity" class="form-control" required>
                <div class="invalid-feedback">
                    Username wajib diisi.
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="loginPassword" class="form-control" required minlength="6">
                    <div class="invalid-feedback">
                        Password minimal 6 karakter.
                    </div>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('loginPassword')">
                        Show
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>

        </form>

        <hr>

        <div class="text-center">
            <small>Belum punya akun?</small><br>
            <a href="/auth/register">Register sebagai Member</a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>