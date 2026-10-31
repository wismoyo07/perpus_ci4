<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Data Master: Klasifikasi DDC</h4>
        <p class="text-muted mb-0 small">Tingkat ditentukan dari kode: <b>X00</b> kelas utama, <b>XY0</b> divisi, lainnya seksi (misalnya 297 Islam). Nomor DDC rinci (misalnya 005.133) diisi per buku dan otomatis memilih klasifikasi paling spesifik.</p>
    </div>
    <a href="/admin/master/sumber-dana" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-coins"></i> Sumber Dana</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="post" action="/admin/master/ddc/store" class="row g-2 align-items-end">
            <?= csrf_field() ?>
            <div class="col-md-2">
                <label class="form-label small fw-medium">Kode *</label>
                <input type="text" name="code_ddc" class="form-control" maxlength="3" pattern="\d{3}" placeholder="370" required value="<?= old('code_ddc') ?>">
            </div>
            <div class="col-md-7">
                <label class="form-label small fw-medium">Deskripsi *</label>
                <input type="text" name="desc_ddc" class="form-control" maxlength="255" placeholder="Pendidikan" required value="<?= old('desc_ddc') ?>">
            </div>
            <div class="col-md-3">
                <button class="btn btn-success w-100"><i class="fa-solid fa-plus"></i> Tambah</button>
            </div>
        </form>
    </div>
</div>

<div class="btn-group btn-group-sm mb-3">
    <a href="/admin/master/ddc" class="btn <?= $tingkat === 'semua' ? 'btn-primary' : 'btn-outline-primary' ?>">Semua</a>
    <a href="/admin/master/ddc?tingkat=kelas" class="btn <?= $tingkat === 'kelas' ? 'btn-primary' : 'btn-outline-primary' ?>">Kelas utama saja</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small text-muted">
                    <tr>
                        <th class="ps-3" width="120">Kode</th>
                        <th width="100">Tingkat</th>
                        <th>Deskripsi</th>
                        <th width="110" class="text-center">Buku</th>
                        <th width="110">Status</th>
                        <th width="230" class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <?php $level = \App\Libraries\DdcData::level($r['code_ddc']); ?>
                        <tr class="<?= $r['is_active'] ? ($level === 'kelas' ? 'table-light fw-semibold' : '') : 'table-secondary' ?>">
                            <td class="ps-3">
                                <form id="ddc-<?= $r['id'] ?>" method="post" action="/admin/master/ddc/update/<?= $r['id'] ?>"><?= csrf_field() ?></form>
                                <input type="text" name="code_ddc" form="ddc-<?= $r['id'] ?>" class="form-control form-control-sm" maxlength="3" pattern="\d{3}" required value="<?= esc($r['code_ddc']) ?>">
                            </td>
                            <td><span class="badge <?= ['kelas' => 'bg-primary', 'divisi' => 'bg-info text-dark', 'seksi' => 'bg-light text-dark border'][$level] ?>"><?= ucfirst($level) ?></span></td>
                            <td>
                                <input type="text" name="desc_ddc" form="ddc-<?= $r['id'] ?>" class="form-control form-control-sm" maxlength="255" required value="<?= esc($r['desc_ddc']) ?>">
                            </td>
                            <td class="text-center"><?= (int) $r['jumlah_buku'] ?></td>
                            <td>
                                <span class="badge <?= $r['is_active'] ? 'bg-success' : 'bg-secondary' ?>"><?= $r['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
                            </td>
                            <td class="text-end pe-3">
                                <button type="submit" form="ddc-<?= $r['id'] ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                                <form method="post" action="/admin/master/ddc/toggle/<?= $r['id'] ?>" class="d-inline">
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
