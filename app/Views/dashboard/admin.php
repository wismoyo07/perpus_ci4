<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <h2 class="text-dark fw-bold">Dashboard Admin</h2>
    <p class="text-muted">Ringkasan status dan aktivitas sistem perpustakaan digital.</p>
</div>

<!-- Notika Status Area Start -->
<div class="notika-status-area">
    <div class="row">
        <!-- Total User Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="wb-traffic-inner">
                <div class="website-traffic-ctn">
                    <h2><span class="counter"><?= esc($total_users) ?></span></h2>
                    <p>Total User</p>
                </div>
                <div class="sparkline-bar-stats1">
                    <i class="fa-solid fa-users text-success" style="font-size: 28px;"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Buku Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="wb-traffic-inner">
                <div class="website-traffic-ctn">
                    <h2><span class="counter"><?= esc($total_books) ?></span></h2>
                    <p>Total Buku</p>
                </div>
                <div class="sparkline-bar-stats2">
                    <i class="fa-solid fa-book text-info" style="font-size: 28px;"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Transaksi Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="wb-traffic-inner">
                <div class="website-traffic-ctn">
                    <h2><span class="counter"><?= esc($total_trx) ?></span></h2>
                    <p>Total Transaksi</p>
                </div>
                <div class="sparkline-bar-stats3">
                    <i class="fa-solid fa-arrow-right-arrow-left text-warning" style="font-size: 28px;"></i>
                </div>
            </div>
        </div>
        
        <!-- Terlambat Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="wb-traffic-inner">
                <div class="website-traffic-ctn">
                    <h2><span class="counter"><?= esc($total_late) ?></span></h2>
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