<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <h2 class="text-dark fw-bold">Daftar Buku Tersedia</h2>
    <p class="text-muted">Kelola koleksi buku, data inventaris fisik, pencarian dan penapisan status buku.</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 shadow-sm mb-3">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-3">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- Filter & Search Card Start -->
<div class="card shadow-sm border-0 mb-4 rounded-3">
    <div class="card-body">
        <form method="get" action="/books" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="search" class="form-label fw-bold text-muted small">Cari Buku</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" id="search" class="form-control border-start-0 ps-0" placeholder="Ketik judul, pengarang, penerbit, atau kode..." value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label fw-bold text-muted small">Status Buku</label>
                <select name="status" id="status" class="form-select">
                    <option value="" <?= ($status === '') ? 'selected' : '' ?>>Semua Status</option>
                    <option value="active" <?= ($status === 'active') ? 'selected' : '' ?>>Aktif</option>
                    <option value="archived" <?= ($status === 'archived') ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-success flex-fill py-2 fw-bold d-flex align-items-center justify-content-center gap-1">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
                <a href="/books" class="btn btn-outline-secondary py-2 fw-bold d-flex align-items-center justify-content-center gap-1">
                    <i class="fa-solid fa-arrows-rotate"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>
<!-- Filter & Search Card End -->

<!-- Books Table Card Start -->
<div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
        <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-book text-success me-2"></i> Inventaris Buku</h5>
        <a href="/books/create" class="btn btn-success btn-sm d-flex align-items-center gap-1 fw-bold">
            <i class="fa-solid fa-plus"></i> Tambah Buku
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7 text-muted">
                    <tr>
                        <th class="ps-3" width="60">No</th>
                        <th width="150">Kode</th>
                        <th>Judul</th>
                        <th width="120">Stok</th>
                        <th width="120">Status</th>
                        <th class="text-end pe-3" width="350">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($books)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada data buku yang ditemukan.</td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $page = $pager->getCurrentPage();
                        $perPage = 10;
                        $i = ($page - 1) * $perPage + 1;
                        ?>
                        <?php foreach ($books as $b): ?>
                            <tr>
                                <td class="ps-3 text-muted"><?= $i++ ?></td>
                                <td class="fw-bold"><?= esc($b['kode_buku']) ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($b['judul']) ?></div>
                                    <small class="text-muted">Pengarang: <?= esc($b['pengarang'] ?? '-') ?></small>
                                    <?php $panggil = \App\Services\BookService::nomorPanggil($b); ?>
                                    <div><small class="<?= $panggil ? 'text-primary fw-semibold' : 'text-muted fst-italic' ?>"><?= $panggil ? 'No. Panggil: ' . esc($panggil) : 'Belum diklasifikasi DDC' ?></small></div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= esc($b['stok_tersedia']) ?> / <?= esc($b['stok_total']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($b['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary">Archived</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-end gap-1 pe-3">
                                        <?php if ($b['is_active']): ?>
                                            <!-- INVENTARIS -->
                                            <a href="/books/items/<?= $b['id'] ?>" class="btn btn-sm btn-info text-white d-flex align-items-center gap-1">
                                                <i class="fa-solid fa-box"></i> Inventaris
                                            </a>

                                            <!-- EDIT -->
                                            <a href="/books/edit/<?= $b['id'] ?>" class="btn btn-sm btn-warning d-flex align-items-center gap-1 text-dark">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>

                                            <!-- Archive -->
                                            <form action="/books/archive/<?= $b['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Archive buku ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-secondary d-flex align-items-center gap-1">
                                                    <i class="fa-solid fa-box-archive"></i> Archive
                                                </button>
                                            </form>

                                            <!-- DELETE -->
                                            <form action="/books/delete/<?= $b['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin hapus buku ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center gap-1">
                                                    <i class="fa-solid fa-trash-can"></i> Hapus
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <!-- Restore -->
                                            <form action="/books/restore/<?= $b['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Aktifkan kembali buku ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-success d-flex align-items-center gap-1 text-white">
                                                    <i class="fa-solid fa-arrow-rotate-left"></i> Restore
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Books Table Card End -->

<div class="d-flex justify-content-center">
    <?= $pager->links('default', 'notika') ?>
</div>

<?= $this->endSection() ?>