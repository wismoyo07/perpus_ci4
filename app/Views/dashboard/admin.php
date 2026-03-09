<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<h2>Dashboard Admin</h2>

<div class="row mt-4">

    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                Total User
                <h3><?= $total_users ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                Total Buku
                <h3><?= $total_books ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                Total Transaksi
                <h3><?= $total_trx ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                Terlambat
                <h3><?= $total_late ?></h3>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>