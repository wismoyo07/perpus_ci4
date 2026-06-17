<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<h2>Manajemen Buku</h2>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<a href="/books/create" class="btn btn-primary mb-3">
    + Tambah Buku
</a>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Judul</th>
            <th>Stok</th>
            <th>Status</th>
            <th width="320">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($books as $b): ?>
            <tr>
                <td><?= esc($b['kode_buku']) ?></td>
                <td><?= esc($b['judul']) ?></td>
                <td><?= $b['stok_tersedia'] ?>/<?= $b['stok_total'] ?></td>
                <td>
                    <?php if($b['is_active']): ?>
                        <span class="badge bg-success">Aktif</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Archived</span>
                    <?php endif; ?>
                </td>
                <td class="d-flex gap-1">

                    <?php if($b['is_active']): ?>

                        <!-- INVENTARIS -->
                        <a href="/books/items/<?= $b['id'] ?>"
                           class="btn btn-sm btn-info text-white">
                            <i class="bi bi-box"></i> Inventaris
                        </a>

                        <!-- EDIT -->
                        <a href="/books/edit/<?= $b['id'] ?>"
                        class="btn btn-sm btn-warning">
                        Edit
                        </a>

                        <!-- Archive -->
                        <form action="/books/archive/<?= $b['id'] ?>"
                            method="post"
                            onsubmit="return confirm('Archive buku ini?')">

                            <?= csrf_field() ?>

                            <button type="submit"
                                    class="btn btn-sm btn-secondary">
                                Archive
                            </button>
                        </form>

                        
                        <!-- DELETE -->
                        <form action="/books/delete/<?= $b['id'] ?>"
                            method="post"
                            onsubmit="return confirm('Yakin hapus buku ini?')">
                            
                            <?= csrf_field() ?>

                            <button type="submit"
                                    class="btn btn-sm btn-danger">
                                Delete
                            </button>
                        </form>

                    <?php else: ?>

                        <!-- Restore -->
                        <form action="/books/restore/<?= $b['id'] ?>"
                            method="post"
                            onsubmit="return confirm('Aktifkan kembali buku ini?')">

                            <?= csrf_field() ?>

                            <button type="submit"
                                    class="btn btn-sm btn-success">
                                Restore
                            </button>
                        </form>

                    <?php endif; ?>

                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $pager->links() ?>

<?= $this->endSection() ?>