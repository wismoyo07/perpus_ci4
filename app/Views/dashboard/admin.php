<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <h2 class="text-dark fw-bold">Dashboard Admin</h2>
    <p class="text-muted">Ringkasan status dan aktivitas sistem perpustakaan digital.</p>
</div>

<!-- Notika Status Area Start -->
<div class="notika-status-area mb-4">
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

<div class="row">
    <!-- Main Content Area: Tables (8 Columns) -->
    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 mb-4">
        
        <!-- Transaksi Sirkulasi Terbaru Card -->
        <div class="card shadow-sm border-0 mb-4 rounded-3 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-arrow-right-arrow-left text-success me-2"></i> Transaksi Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase fs-7 text-muted">
                            <tr>
                                <th class="ps-3">Kode</th>
                                <th>Nama Anggota</th>
                                <th>Tgl Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Denda</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_trx)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi sirkulasi terbaru.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_trx as $trx): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-success"><?= esc($trx['kode_transaksi']) ?></td>
                                        <td><?= esc($trx['member_name'] ?? 'Umum') ?></td>
                                        <td><?= date('d M Y', strtotime($trx['tanggal_pinjam'])) ?></td>
                                        <td><?= date('d M Y', strtotime($trx['jatuh_tempo'])) ?></td>
                                        <td>
                                            <?php if ($trx['status'] === 'dipinjam'): ?>
                                                <span class="badge bg-info text-white">Dipinjam</span>
                                            <?php elseif ($trx['status'] === 'terlambat'): ?>
                                                <span class="badge bg-danger text-white">Terlambat</span>
                                            <?php else: ?>
                                                <span class="badge bg-success text-white">Selesai</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-3 text-danger fw-bold">Rp <?= number_format($trx['total_denda'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Log Aktivitas Terbaru Card -->
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-list-check text-success me-2"></i> Aktivitas Sistem Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase fs-7 text-muted">
                            <tr>
                                <th class="ps-3" width="180">Waktu</th>
                                <th>Pengguna</th>
                                <th class="pe-3">Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_logs)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Belum ada aktivitas log tercatat.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_logs as $log): ?>
                                    <tr>
                                        <td class="ps-3 text-muted"><?= date('d M Y H:i', strtotime($log['created_at'] ?? 'now')) ?></td>
                                        <td class="fw-bold"><?= esc($log['user_name'] ?? 'System') ?></td>
                                        <td class="pe-3"><?= esc($log['aktivitas']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Sidebar Area: Quick Action (4 Columns) -->
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-4">
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-bolt text-warning me-2"></i> Akses Cepat</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Tindakan administratif cepat yang memerlukan perhatian Admin:</p>
                <a href="/users" class="btn btn-success w-100 py-3 mb-2 d-flex align-items-center justify-content-center gap-2 rounded-3 fw-bold shadow-sm">
                    <i class="fa-solid fa-user-check" style="font-size: 18px;"></i>
                    <span>Validasi Anggota Baru</span>
                </a>
                <div class="text-center mt-3">
                    <small class="text-muted">Gunakan menu navigasi atas untuk fitur administratif lainnya.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>