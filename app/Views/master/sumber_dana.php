<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Data Master: Sumber Dana</h4>
        <p class="text-muted mb-0 small">Kode sumber dana menjadi bagian kode buku dan kode inventaris, misalnya <b>03.11.01.01.001 / <u>BOS</u> / 2026 / 001</b>.</p>
    </div>
    <a href="/admin/master/ddc" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-layer-group"></i> Klasifikasi DDC</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="post" action="/admin/master/sumber-dana/store" class="row g-2 align-items-end">
            <?= csrf_field() ?>
            <div class="col-md-3">
                <label class="form-label small fw-medium">Kode *</label>
                <input type="text" name="kode" class="form-control text-uppercase" maxlength="20" pattern="[A-Za-z0-9-]{2,20}" placeholder="DAK" required value="<?= old('kode') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-medium">Nama Sumber Dana *</label>
                <input type="text" name="source_name" class="form-control" maxlength="100" placeholder="Dana Alokasi Khusus" required value="<?= old('source_name') ?>">
            </div>
            <div class="col-md-3">
                <button class="btn btn-success w-100"><i class="fa-solid fa-plus"></i> Tambah</button>
            </div>
        </form>
        <small class="text-muted">Kode tidak bisa diubah setelah dibuat. Sumber dana yang tidak dipakai lagi cukup dinonaktifkan.</small>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small text-muted">
                    <tr>
                        <th class="ps-3" width="140">Kode</th>
                        <th>Nama</th>
                        <th width="120" class="text-center">Salinan</th>
                        <th width="110">Status</th>
                        <th width="230" class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr class="<?= $r['is_active'] ? '' : 'table-secondary' ?>">
                            <td class="ps-3 fw-bold"><code><?= esc($r['kode']) ?></code></td>
                            <td>
                                <form id="sf-<?= $r['id'] ?>" method="post" action="/admin/master/sumber-dana/update/<?= $r['id'] ?>"><?= csrf_field() ?></form>
                                <input type="text" name="source_name" form="sf-<?= $r['id'] ?>" class="form-control form-control-sm" maxlength="100" required value="<?= esc($r['source_name']) ?>">
                            </td>
                            <td class="text-center"><?= (int) $r['jumlah_salinan'] ?></td>
                            <td>
                                <span class="badge <?= $r['is_active'] ? 'bg-success' : 'bg-secondary' ?>"><?= $r['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
                            </td>
                            <td class="text-end pe-3">
                                <button type="submit" form="sf-<?= $r['id'] ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                                <form method="post" action="/admin/master/sumber-dana/toggle/<?= $r['id'] ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm <?= $r['is_active'] ? 'btn-outline-secondary' : 'btn-outline-success' ?>">
                                        <?= $r['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
