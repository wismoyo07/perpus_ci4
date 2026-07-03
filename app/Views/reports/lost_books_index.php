<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <!-- HEADER & EXPORT ACTIONS -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2"><i class="bi bi-exclamation-triangle text-danger"></i> Laporan Buku Hilang & Ganti Rugi</h1>
            <p class="text-muted mb-0">Pertanggungjawaban sirkulasi aset buku yang hilang dan status penyelesaiannya.</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0 d-flex gap-2">
            <!-- EXCEL EXPORT -->
            <a href="/reports/lost-books/export?format=excel&status=<?= esc($status) ?>&start_date=<?= esc($start_date) ?>&end_date=<?= esc($end_date) ?>" 
               class="btn btn-success shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
            </a>
            <!-- PRINT / PDF -->
            <a href="/reports/lost-books/export?format=print&status=<?= esc($status) ?>&start_date=<?= esc($start_date) ?>&end_date=<?= esc($end_date) ?>" 
               target="_blank" 
               class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-printer-fill"></i> Cetak Laporan Resmi
            </a>
            <a href="/reports" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- STATS SUMMARY CARDS -->
    <div class="row g-3 mb-4">
        <!-- Total Hilang -->
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-danger text-white h-100">
                <div class="card-body py-3">
                    <h6 class="text-white-50 mb-1 small text-uppercase">Buku Hilang</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['total_hilang'] ?? 0) ?></h3>
                    <small class="text-white-50">Total Kasus</small>
                </div>
            </div>
        </div>
        <!-- Pending -->
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-warning text-dark h-100">
                <div class="card-body py-3">
                    <h6 class="text-dark-50 mb-1 small text-uppercase">Belum Selesai</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['total_pending'] ?? 0) ?></h3>
                    <small class="text-dark-50">Butuh Ganti Rugi</small>
                </div>
            </div>
        </div>
        <!-- Replaced -->
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-success text-white h-100">
                <div class="card-body py-3">
                    <h6 class="text-white-50 mb-1 small text-uppercase">Ganti Buku</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['total_replaced'] ?? 0) ?></h3>
                    <small class="text-white-50">Salinan Baru Masuk</small>
                </div>
            </div>
        </div>
        <!-- Paid -->
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-info text-white h-100">
                <div class="card-body py-3">
                    <h6 class="text-white-50 mb-1 small text-uppercase">Ganti Uang</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['total_paid'] ?? 0) ?></h3>
                    <small class="text-white-50">Denda Finansial</small>
                </div>
            </div>
        </div>
        <!-- Total Denda Masuk -->
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-dark text-white h-100">
                <div class="card-body py-3">
                    <h6 class="text-white-50 mb-1 small text-uppercase">Uang Masuk</h6>
                    <h3 class="mb-0 fw-bold text-success">Rp <?= number_format($stats['total_amount'] ?? 0, 0, ',', '.') ?></h3>
                    <small class="text-white-50">Total Kas Diterima</small>
                </div>
            </div>
        </div>
        <!-- Written Off -->
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-secondary text-white h-100">
                <div class="card-body py-3">
                    <h6 class="text-white-50 mb-1 small text-uppercase">Pemutihan</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['total_written_off'] ?? 0) ?></h3>
                    <small class="text-white-50">Dihapus Permanen</small>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light rounded-3">
            <form method="get" action="/reports/lost-books" class="row g-3 align-items-end">
                <!-- Status -->
                <div class="col-md-3">
                    <label for="status" class="form-label small fw-semibold text-secondary">Status Penyelesaian</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending (Belum Selesai)</option>
                        <option value="replaced" <?= $status === 'replaced' ? 'selected' : '' ?>>Ganti Buku Fisik</option>
                        <option value="paid" <?= $status === 'paid' ? 'selected' : '' ?>>Ganti Uang (Denda)</option>
                        <option value="written_off" <?= $status === 'written_off' ? 'selected' : '' ?>>Pemutihan</option>
                    </select>
                </div>
                <!-- Rentang Awal -->
                <div class="col-md-3">
                    <label for="start_date" class="form-label small fw-semibold text-secondary">Dari Tanggal Hilang</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= esc($start_date) ?>">
                </div>
                <!-- Rentang Akhir -->
                <div class="col-md-3">
                    <label for="end_date" class="form-label small fw-semibold text-secondary">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= esc($end_date) ?>">
                </div>
                <!-- Tombol Aksi -->
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-filter"></i> Saring</button>
                    <a href="/reports/lost-books" class="btn btn-outline-secondary w-100 fw-semibold"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- DATA TABLE -->
    <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-muted">
                        <tr>
                            <th class="ps-4" width="60">No</th>
                            <th width="130">Tgl Hilang</th>
                            <th width="140">Kode Transaksi</th>
                            <th width="180">Peminjam</th>
                            <th width="180">Buku / Kode Inv Asal</th>
                            <th width="130">Metode Ganti</th>
                            <th width="120">Denda Uang</th>
                            <th width="220">Catatan Penyelesaian</th>
                            <th class="pe-4" width="120">Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lostBooks)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x display-6 d-block mb-3 text-muted"></i>
                                    Tidak ada data buku hilang yang cocok dengan filter.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $perPage = 10;
                            $i = ($page - 1) * $perPage + 1;
                            ?>
                            <?php foreach ($lostBooks as $l): ?>
                                <tr>
                                    <td class="ps-4 text-muted"><?= $i++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($l['tanggal_hilang'])) ?></td>
                                    <td class="fw-bold text-dark"><?= esc($l['kode_transaksi']) ?></td>
                                    <td>
                                        <strong><?= esc(preg_replace('/\s*\(siswa\)|\s*\(guru\)|\s*\(pegawai\)/i', '', $l['member_name'])) ?></strong><br>
                                        <small class="badge bg-light text-dark border small-text"><?= esc($l['jenis_pengguna']) ?></small>
                                    </td>
                                    <td>
                                        <strong><?= esc($l['judul']) ?></strong><br>
                                        <small class="text-muted">Inv: <code><?= esc($l['original_kode_inventaris'] ?: '-') ?></code></small>
                                    </td>
                                    <td>
                                        <?php if($l['lost_settlement_status'] === 'replaced'): ?>
                                            <span class="badge bg-success">Ganti Buku</span>
                                        <?php elseif($l['lost_settlement_status'] === 'paid'): ?>
                                            <span class="badge bg-info text-white">Ganti Uang</span>
                                        <?php elseif($l['lost_settlement_status'] === 'written_off'): ?>
                                            <span class="badge bg-secondary">Pemutihan</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold <?= $l['lost_settlement_status'] === 'paid' ? 'text-dark' : 'text-muted' ?>">
                                        <?= $l['lost_settlement_status'] === 'paid' ? 'Rp '.number_format($l['lost_settlement_amount'], 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="small text-secondary" style="max-width: 220px; white-space: normal;">
                                        <?= esc($l['lost_note'] ?: '-') ?>
                                    </td>
                                    <td class="pe-4 text-secondary small">
                                        <?php if (!empty($l['verifier_name'])): ?>
                                            <i class="bi bi-person-fill-check text-success"></i> <?= esc($l['verifier_name']) ?><br>
                                            <small class="text-muted"><?= date('d/m/y H:i', strtotime($l['settled_at'])) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
        <?= $pager ?>
    </div>

</div>

<?= $this->endSection() ?>
