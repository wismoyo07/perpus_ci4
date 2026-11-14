<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$badge = [
    'baru'     => ['bg-success', 'Baru'],
    'perbarui' => ['bg-primary', 'Diperbarui'],
    'lewati'   => ['bg-secondary', 'Dilewati'],
    'error'    => ['bg-danger', 'Error'],
];
$saved = $summary['baru'] + $summary['perbarui'];
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h4 class="text-dark fw-bold"><i class="fa-solid fa-list-check text-success me-2"></i> Pratinjau Impor Anggota</h4>
            <p class="text-muted mb-0">
                File <b><?= esc($filename) ?></b> · NIS / NIP yang sudah terdaftar: <b><?= $mode === 'perbarui' ? 'diperbarui' : 'dilewati' ?></b>.
                Data <b>belum disimpan</b>.
            </p>
        </div>
        <a href="/users/import" class="btn btn-outline-secondary shadow-sm"><i class="fa-solid fa-arrow-left me-1"></i> Unggah Ulang</a>
    </div>

    <div class="row g-3 mb-4">
        <?php foreach ($badge as $key => [$cls, $label]): ?>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3">
                        <span class="badge <?= $cls ?> mb-1"><?= $label ?></span>
                        <h3 class="mb-0 fw-bold"><?= (int) $summary[$key] ?></h3>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($summary['error'] > 0): ?>
        <div class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation me-1"></i>
            <b><?= (int) $summary['error'] ?> baris error tidak akan diimpor.</b> Perbaiki di file Excel lalu unggah ulang,
            atau lanjutkan untuk menyimpan baris yang valid saja.
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light text-uppercase text-muted">
                    <tr>
                        <th class="ps-3">Baris</th>
                        <th>Status</th>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th>NIS / NIP</th>
                        <th>Kelas</th>
                        <th>Username</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($analysis as $a): ?>
                        <?php [$cls, $label] = $badge[$a['status']]; ?>
                        <tr class="<?= $a['status'] === 'error' ? 'table-danger' : '' ?>">
                            <td class="ps-3"><?= (int) $a['row'] ?></td>
                            <td><span class="badge <?= $cls ?>"><?= $label ?></span></td>
                            <td class="fw-semibold"><?= esc($a['data']['name']) ?></td>
                            <td><?= esc(ucfirst($a['data']['jenis_pengguna'])) ?></td>
                            <td><code><?= esc($a['data']['member_code']) ?></code></td>
                            <td><?= esc($a['data']['class'] ?: '-') ?></td>
                            <td><?= esc($a['data']['username'] ?: '-') ?></td>
                            <td>
                                <?php foreach ($a['errors'] as $e): ?>
                                    <div class="text-danger"><i class="fa-solid fa-circle-xmark me-1"></i><?= esc($e) ?></div>
                                <?php endforeach; ?>
                                <?php foreach ($a['notes'] as $n): ?>
                                    <div class="text-muted"><?= esc($n) ?></div>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <form method="post" action="/users/import/process" class="d-flex justify-content-end gap-2 mb-5">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= esc($token) ?>">
        <a href="/users/import" class="btn btn-outline-secondary">Batal</a>
        <button type="submit" class="btn btn-success fw-bold" <?= $saved === 0 ? 'disabled' : '' ?>
                onclick="return confirm('Simpan <?= $saved ?> anggota (<?= (int) $summary['baru'] ?> baru, <?= (int) $summary['perbarui'] ?> diperbarui)?');">
            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan <?= $saved ?> Anggota
        </button>
    </form>
</div>

<?= $this->endSection() ?>
