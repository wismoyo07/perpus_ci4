<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <h2 class="text-dark fw-bold">Dashboard Anggota</h2>
    <p class="text-muted">Selamat datang di portal anggota perpustakaan digital Anda.</p>
</div>

<!-- Notika Status Area Start -->
<div class="notika-status-area">
    <div class="row">
        <!-- Buku Dipinjam -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="wb-traffic-inner">
                <div class="website-traffic-ctn" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                    <h2><span class="counter"><?= esc($my_books) ?></span></h2>
                    <p>Buku Sedang Dipinjam</p>
                </div>
                <div class="sparkline-bar-stats2">
                    <i class="fa-solid fa-book-open text-info" style="font-size: 28px;"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Notika Status Area End -->

<?= $this->endSection() ?>