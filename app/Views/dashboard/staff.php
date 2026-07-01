<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <h2 class="text-dark fw-bold">Dashboard Staff</h2>
    <p class="text-muted">Akses cepat ke status transaksi sirkulasi buku perpustakaan.</p>
</div>

<!-- Notika Status Area Start -->
<div class="notika-status-area">
    <div class="row">
        <!-- Transaksi Aktif -->
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="wb-traffic-inner">
                <div class="website-traffic-ctn" style="background: linear-gradient(135deg, #00c292 0%, #00a085 100%);">
                    <h2><span class="counter"><?= esc($trx_active) ?></span></h2>
                    <p>Transaksi Aktif</p>
                </div>
                <div class="sparkline-bar-stats1">
                    <i class="fa-solid fa-arrow-right-arrow-left text-success" style="font-size: 28px;"></i>
                </div>
            </div>
        </div>
        
        <!-- Transaksi Terlambat -->
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="wb-traffic-inner">
                <div class="website-traffic-ctn" style="background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);">
                    <h2><span class="counter"><?= esc($trx_late) ?></span></h2>
                    <p>Transaksi Terlambat</p>
                </div>
                <div class="sparkline-bar-stats4">
                    <i class="fa-solid fa-triangle-exclamation text-danger" style="font-size: 28px;"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Notika Status Area End -->

<?= $this->endSection() ?>