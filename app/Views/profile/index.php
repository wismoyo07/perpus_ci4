<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$isSiswa  = $user['jenis_pengguna'] === 'siswa';
$label1   = $isSiswa ? 'NIS' : 'NIP / Kode Pegawai';
$label2   = $isSiswa ? 'NISN' : 'NUPTK';
$roleName = ['admin' => 'Admin', 'staff' => 'Staf Perpustakaan', 'member' => 'Anggota'][$user['role']] ?? ucfirst($user['role']);
$rp       = static fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
?>

<div class="container-fluid">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h4 class="text-dark fw-bold"><i class="fa-solid fa-circle-user text-primary me-2"></i> Profil Saya</h4>
        <p class="text-muted mb-0">Anda dapat memperbarui kontak dan password. Data identitas hanya dapat diubah oleh petugas perpustakaan.</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- IDENTITAS -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-id-card text-muted me-2"></i> Identitas</h5>
                    <a href="/profile/card" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-print"></i> Cetak Kartu
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-3">
                        <tr><th class="text-secondary fw-semibold" style="width: 40%;">Nama Lengkap</th><td><?= esc($user['name']) ?></td></tr>
                        <tr><th class="text-secondary fw-semibold">Username</th><td><code><?= esc($user['username']) ?></code></td></tr>
                        <tr><th class="text-secondary fw-semibold">Role</th><td><?= esc($roleName) ?></td></tr>
                        <tr><th class="text-secondary fw-semibold">Jenis Pengguna</th><td><?= esc(ucfirst($user['jenis_pengguna'] ?? '-')) ?></td></tr>
                        <tr><th class="text-secondary fw-semibold"><?= $label1 ?></th><td><?= esc($user['member_code'] ?: '-') ?></td></tr>
                        <tr><th class="text-secondary fw-semibold"><?= $label2 ?></th><td><?= esc($user['member_code_nation'] ?: '-') ?></td></tr>
                        <?php if ($isSiswa): ?>
                            <tr><th class="text-secondary fw-semibold">Kelas</th><td><?= esc($user['class'] ?: '-') ?></td></tr>
                        <?php endif; ?>
                        <?php $prevLogin = session('previous_login'); ?>
                        <tr><th class="text-secondary fw-semibold">Login Sebelumnya</th><td><?= $prevLogin ? date('d/m/Y H:i', strtotime($prevLogin)) : '-' ?></td></tr>
                    </table>
                    <small class="text-muted"><i class="fa-solid fa-circle-info"></i> Ada data identitas yang salah? Hubungi petugas perpustakaan.</small>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <?php if ($user['role'] === 'member'): ?>
                <!-- RINGKASAN ANGGOTA -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <a href="/member/transactions" class="text-decoration-none">
                            <div class="card border-0 shadow-sm bg-primary text-white h-100">
                                <div class="card-body py-3">
                                    <h6 class="mb-1 small text-uppercase fw-bold opacity-75">Buku Sedang Dipinjam</h6>
                                    <h3 class="mb-0 fw-bold"><?= (int) $activeBooks ?></h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="/member/fines" class="text-decoration-none">
                            <div class="card border-0 shadow-sm <?= (int) $fineTotals['jml_belum'] > 0 ? 'bg-danger' : 'bg-success' ?> text-white h-100">
                                <div class="card-body py-3">
                                    <h6 class="mb-1 small text-uppercase fw-bold opacity-75">Denda Belum Lunas</h6>
                                    <h3 class="mb-0 fw-bold"><?= $rp($fineTotals['rp_belum']) ?></h3>
                                    <?php if ((float) $fineTotals['rp_tahan'] > 0): ?>
                                        <small class="opacity-75">+ <?= $rp($fineTotals['rp_tahan']) ?> ditahan</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- KONTAK -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-address-book text-muted me-2"></i> Kontak</h5>
                </div>
                <div class="card-body">
                    <?php $contactErrors = (array) session()->getFlashdata('contact_errors'); ?>
                    <?php if ($contactErrors): ?>
                        <div class="alert alert-danger"><ul class="mb-0 ps-3"><?php foreach ($contactErrors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
                    <?php endif; ?>
                    <form method="post" action="/profile/contact" class="row g-3">
                        <?= csrf_field() ?>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="phone">Nomor Telepon</label>
                            <input type="text" name="phone" id="phone" class="form-control" maxlength="20" required
                                   value="<?= esc(old('phone', $user['phone'] ?? '')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="whatsapp">Nomor WhatsApp</label>
                            <input type="text" name="whatsapp" id="whatsapp" class="form-control" maxlength="20" required
                                   value="<?= esc(old('whatsapp', $user['whatsapp'] ?? '')) ?>">
                        </div>
                        <div class="col-12 text-end">
                            <button class="btn btn-primary fw-bold">Simpan Kontak</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- PASSWORD -->
            <div class="card shadow-sm border-0 rounded-3" id="password">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 fw-bold"><i class="fa-solid fa-key text-muted me-2"></i> Ganti Password</h5>
                </div>
                <div class="card-body">
                    <?php $passwordErrors = (array) session()->getFlashdata('password_errors'); ?>
                    <?php if ($passwordErrors): ?>
                        <div class="alert alert-danger"><ul class="mb-0 ps-3"><?php foreach ($passwordErrors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
                    <?php endif; ?>
                    <form method="post" action="/profile/password" class="row g-3">
                        <?= csrf_field() ?>
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="currentPassword">Password Lama</label>
                            <input type="password" name="current_password" id="currentPassword" class="form-control" required autocomplete="current-password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="newPassword">Password Baru</label>
                            <input type="password" name="password" id="newPassword" class="form-control"
                                   required minlength="8" maxlength="64" autocomplete="new-password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="confirmPassword">Konfirmasi Password Baru</label>
                            <input type="password" name="confirm_password" id="confirmPassword" class="form-control"
                                   required maxlength="64" autocomplete="new-password">
                        </div>
                        <div class="col-12">
                            <?= $this->include('auth/_password_hints') ?>
                        </div>
                        <div class="col-12 text-end">
                            <button class="btn btn-warning fw-bold">Ganti Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
