<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Daftar Transaksi</h4>
        <a href="/transactions/create" class="btn btn-primary btn-sm">
            + Transaksi Baru
        </a>
    </div>

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

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Member</th>
                <th>Jenis</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
                <th>Denda</th>
                <th>Staff</th>
                <th width="150">Aksi</th>
            </tr>
        </thead>
        
        <tbody>
            <?php foreach ($transactions as $trx): ?>
            <tr>
                <td><?= esc($trx['kode_transaksi']) ?></td>

                <td><?= esc($trx['member_name']) ?></td>

                <td>
                    <span class="badge bg-secondary">
                        <?= esc($trx['jenis_pengguna']) ?>
                    </span>
                </td>

                <td><?= $trx['tanggal_pinjam'] ?></td>
                <td><?= $trx['jatuh_tempo'] ?></td>

                <td>
                    <?php if($trx['status'] === 'dipinjam'): ?>
                        <span class="badge bg-primary">Dipinjam</span>
                    <?php elseif($trx['status'] === 'terlambat'): ?>
                        <span class="badge bg-danger">Terlambat</span>
                    <?php else: ?>
                        <span class="badge bg-success">Selesai</span>
                    <?php endif; ?>
                </td>

                <td>Rp <?= number_format($trx['total_denda'],0,',','.') ?></td>

                <td>
                    <small>
                        Pinjam: <?= esc($trx['staff_borrow']) ?><br>
                        <?php if($trx['staff_return']): ?>
                            Kembali: <?= esc($trx['staff_return']) ?>
                        <?php endif; ?>
                    </small>
                </td>

                <td>
                    <?php if($trx['status'] !== 'selesai'): ?>
                        <a href="/transactions/return/<?= $trx['id'] ?>"
                        class="btn btn-sm btn-warning">
                        Return
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?= $pager->links() ?>

</div>

<?= $this->endSection() ?>