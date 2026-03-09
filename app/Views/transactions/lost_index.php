<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h4>Tracking Buku Hilang</h4>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Kode Transaksi</th>
            <th>Member</th>
            <th>Buku</th>
            <th>Tgl Pinjam</th>
            <th>Status</th>
            <th width="150">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lostBooks as $l): ?>
        <tr>
            <td><?= esc($l['kode_transaksi']) ?></td>
            <td><?= esc($l['member_name']) ?></td>
            <td><?= esc($l['judul']) ?></td>
            <td><?= $l['tanggal_pinjam'] ?></td>

            <td>
                <?php if($l['lost_settlement_status'] === 'replaced'): ?>
                    <span class="badge bg-success">Replaced</span>
                <?php else: ?>
                    <span class="badge bg-danger">Pending</span>
                <?php endif; ?>
            </td>

            <td>
                <?php if($l['lost_settlement_status'] !== 'replaced'): ?>
                    <form method="post"
                          action="/lost-books/settle/<?= $l['id'] ?>">
                        <?= csrf_field() ?>
                        <button type="submit"
                                class="btn btn-sm btn-primary">
                            Tandai Diganti
                        </button>
                    </form>
                <?php endif; ?>
            </td>

        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>