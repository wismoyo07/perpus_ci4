<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h4>Monitoring Transaksi</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Kode</th>
            <th>User</th>
            <th>Status</th>
            <th>Denda</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $trx): ?>
            <tr>
                <td><?= esc($trx['kode_transaksi']) ?></td>
                <td><?= esc($trx['member_name']) ?></td>
                <td><?= esc($trx['status']) ?></td>
                <td>Rp <?= number_format($trx['total_denda'],0,',','.') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $pager->links() ?>

<?= $this->endSection() ?>