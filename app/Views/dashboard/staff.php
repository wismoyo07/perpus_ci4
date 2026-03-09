<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<h2>Dashboard Staff</h2>

<div class="row mt-4">

    <div class="col-md-6">
        <div class="card bg-info text-white">
            <div class="card-body">
                Transaksi Aktif
                <h3><?= $trx_active ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card bg-danger text-white">
            <div class="card-body">
                Transaksi Terlambat
                <h3><?= $trx_late ?></h3>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>