<?= $this->extend('layout/auth') ?>
<?= $this->section('content') ?>

<!-- Login -->
<div class="nk-auth-card toggled">
    <div class="nk-auth-card-header">
        <h2>Selamat Datang</h2>
        <p>Masukan Username dan Password Akun Anda</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>


    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>


    <form method="post" action="<?= site_url('auth/login') ?>" class="nk-auth-form" novalidate>
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="loginUsername" class="form-label">Username atau NIS / NIP</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                <input type="text" class="form-control" name="identity" placeholder="Enter your username" required>
            </div>
            <div class="invalid-feedback">Ketikan Username atau NIS/NIP.</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control" id="loginPassword" name="password"
                    placeholder="Enter your password" required autocomplete="current-password" minlength="6">
                <button type="button" class="input-group-text nk-toggle-password"
                    aria-label="Toggle password visibility" data-target="loginPassword">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            <div class="invalid-feedback">Ketikan Password Anda, Minimal 6 karakter.</div>
        </div>

        <div class="text-end mb-4">
            <small class="text-muted">Lupa password? Hubungi petugas perpustakaan untuk reset.</small>
        </div>

        <button type="submit" class="btn btn-primary w-100 nk-auth-btn">
            <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Sign In
        </button>
    </form>

    <div class="nk-auth-footer">
        <p>Belum Punya Akun? <a href="<?= site_url('auth/register') ?>">Registrasi disini</a></p>
    </div>
</div>

<?= $this->endSection() ?>