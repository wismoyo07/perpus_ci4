<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-file-earmark-bar-graph text-primary"></i> Laporan Transaksi & Inventaris</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/lost-books" class="btn btn-danger shadow-sm me-2">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> Laporan Buku Hilang
        </a>
        <a href="/reports/print" target="_blank" class="btn btn-primary shadow-sm">
            <i class="bi bi-printer-fill me-1"></i> Cetak Laporan Resmi
        </a>
    </div>
</div>

<!-- Widget Statistik Ringkasan -->
<div class="row g-3 mb-4">
    <!-- Total Buku -->
    <div class="col-md-4 col-lg-3">
        <div class="card border-0 shadow-sm h-100 bg-primary text-white">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-1">Total Buku (Stok)</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($total_books) ?></h3>
                    <small class="text-white-50"><?= $total_titles ?> Judul Buku</small>
                </div>
                <div class="fs-1 text-white-50"><i class="bi bi-bookshelf"></i></div>
            </div>
        </div>
    </div>

    <!-- Tersedia -->
    <div class="col-md-4 col-lg-3">
        <div class="card border-0 shadow-sm h-100 bg-success text-white">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-1">Buku Tersedia</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($available_books) ?></h3>
                    <small class="text-white-50">Siap Dipinjam</small>
                </div>
                <div class="fs-1 text-white-50"><i class="bi bi-journal-check"></i></div>
            </div>
        </div>
    </div>

    <!-- Dipinjam -->
    <div class="col-md-4 col-lg-3">
        <div class="card border-0 shadow-sm h-100 bg-warning text-white">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-1">Sedang Dipinjam</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($borrowed_books) ?></h3>
                    <small class="text-white-50">Sirkulasi Aktif</small>
                </div>
                <div class="fs-1 text-white-50"><i class="bi bi-arrow-left-right"></i></div>
            </div>
        </div>
    </div>

    <!-- Hilang -->
    <div class="col-md-4 col-lg-3">
        <div class="card border-0 shadow-sm h-100 bg-danger text-white">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-1">Buku Hilang</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($lost_books) ?></h3>
                    <small class="text-white-50">Butuh Penggantian</small>
                </div>
                <div class="fs-1 text-white-50"><i class="bi bi-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Peminjaman Jangka Pendek vs Panjang -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title mb-0 text-secondary fw-semibold">Detail Kategori Peminjaman Aktif</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                        <div>
                            <h6 class="mb-0">Peminjaman Jangka Pendek</h6>
                            <small class="text-muted">Durasi pinjam &le; 30 Hari (Buku Umum/Referensi)</small>
                        </div>
                        <span class="badge bg-info rounded-pill fs-6"><?= $short_term ?> Buku</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                        <div>
                            <h6 class="mb-0">Peminjaman Jangka Panjang</h6>
                            <small class="text-muted">Durasi pinjam > 30 Hari (Buku Pelajaran/Paket)</small>
                        </div>
                        <span class="badge bg-secondary rounded-pill fs-6"><?= $long_term ?> Buku</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Metadata General -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title mb-0 text-secondary fw-semibold">Metadata Klasifikasi</h5>
            </div>
            <div class="card-body d-flex align-items-center">
                <div class="row w-100 text-center">
                    <div class="col-6 border-end">
                        <h2 class="text-primary fw-bold mb-0"><?= $total_categories ?></h2>
                        <small class="text-muted">Kelas DDC Terdaftar<br><span class="small">+ <?= (int) $total_ddc_detail ?> divisi/seksi</span></small>
                    </div>
                    <div class="col-6">
                        <?php $belum = array_sum(array_map(static fn ($r) => $r['code_ddc'] === '-' ? (int) $r['judul'] : 0, $ddc_distribution)); ?>
                        <h2 class="<?= $belum ? 'text-warning' : 'text-success' ?> fw-bold mb-0"><?= $belum ?></h2>
                        <small class="text-muted">Judul Belum Diklasifikasi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sebaran Koleksi per Kelas DDC -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="card-title mb-0 text-secondary fw-semibold">Sebaran Koleksi per Kelas DDC</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" width="90">Kelas</th>
                        <th>Deskripsi</th>
                        <th class="text-center" width="120">Judul</th>
                        <th class="text-center pe-3" width="120">Eksemplar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ddc_distribution as $d): ?>
                        <tr class="<?= $d['code_ddc'] === '-' ? 'table-warning' : '' ?>">
                            <td class="ps-3 fw-bold"><?= esc($d['code_ddc']) ?></td>
                            <td><?= esc($d['desc_ddc']) ?></td>
                            <td class="text-center"><?= (int) $d['judul'] ?></td>
                            <td class="text-center pe-3"><?= (int) $d['eksemplar'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Rincian per Divisi / Seksi DDC -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="card-title mb-0 text-secondary fw-semibold">Rincian Koleksi per Divisi / Seksi DDC</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" width="90">Kode</th>
                        <th>Klasifikasi</th>
                        <th class="text-center" width="120">Judul</th>
                        <th class="text-center pe-3" width="120">Eksemplar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ddc_detail)): ?>
                        <tr><td colspan="4" class="text-center text-muted">Belum ada buku yang diklasifikasi.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($ddc_detail as $d): ?>
                        <tr>
                            <td class="ps-3 fw-bold"><?= esc($d['code_ddc']) ?></td>
                            <td><?= esc($d['desc_ddc']) ?></td>
                            <td class="text-center"><?= (int) $d['judul'] ?></td>
                            <td class="text-center pe-3"><?= (int) $d['eksemplar'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Ringkasan Denda -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 text-secondary fw-semibold">Ringkasan Denda</h5>
        <a href="/fines?status=semua" class="btn btn-sm btn-outline-secondary">Lihat Menu Denda</a>
    </div>
    <div class="card-body pt-0">
        <div class="row g-3">
            <?php foreach ([
                ['Diterima (Lunas)', 'rp_lunas', 'jml_lunas', 'text-success'],
                ['Belum Dibayar', 'rp_belum', 'jml_belum', 'text-danger'],
                ['Ditahan (Buku Hilang)', 'rp_tahan', 'jml_tahan', 'text-warning'],
                ['Dibebaskan', 'rp_bebas', 'jml_bebas', 'text-secondary'],
            ] as [$label, $rp, $jml, $cls]): ?>
            <div class="col-6 col-md-3">
                <div class="border rounded p-3 h-100">
                    <div class="small text-muted"><?= $label ?></div>
                    <div class="fs-5 fw-bold <?= $cls ?>">Rp <?= number_format((float) $fine_totals[$rp], 0, ',', '.') ?></div>
                    <div class="small text-muted"><?= (int) $fine_totals[$jml] ?> denda</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Riwayat Transaksi Terbaru -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 text-secondary fw-semibold">10 Transaksi Terakhir</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Kode Transaksi</th>
                        <th>Peminjam (Member)</th>
                        <th>Tanggal Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th class="pe-4">Petugas (Staff)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat transaksi.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark"><?= esc($t['kode_transaksi']) ?></td>
                                <td><?= esc($t['member_name']) ?></td>
                                <td><?= date('d/m/Y', strtotime($t['tanggal_pinjam'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($t['jatuh_tempo'])) ?></td>
                                <td>
                                    <?php if ($t['status'] === 'dipinjam'): ?>
                                        <span class="badge bg-warning text-dark">Dipinjam</span>
                                    <?php elseif ($t['status'] === 'terlambat'): ?>
                                        <span class="badge bg-danger">Terlambat</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="fw-medium text-danger">Rp <?= number_format($t['total_denda'], 0, ',', '.') ?></span><?= fine_badge($t['fine'], $t['status']) ?></td>
                                <td class="pe-4"><?= esc($t['staff_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
