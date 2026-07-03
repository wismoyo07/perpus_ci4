<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <!-- HEADER & EXPORT ACTIONS -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h4 class="text-dark fw-bold"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i> Tracking Buku Hilang & Ganti Rugi</h4>
            <p class="text-muted mb-0">Pantau status penyelesaian ganti rugi fisik atau denda finansial untuk buku yang hilang.</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0 d-flex gap-2">
            <!-- EXCEL EXPORT ONLY (PRINT PDF BUTTON REMOVED AS REQUESTED) -->
            <a href="/lost-books/export?format=excel&status=<?= esc($status) ?>&start_date=<?= esc($start_date) ?>&end_date=<?= esc($end_date) ?>" 
               class="btn btn-success shadow-sm d-flex align-items-center gap-2 fw-bold">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- STATS SUMMARY CARDS -->
    <div class="row g-3 mb-4">
        <!-- Total Hilang -->
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm bg-danger text-white h-100">
                <div class="card-body py-3">
                    <h6 class="text-white-50 mb-1 small text-uppercase fw-bold">Buku Hilang</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['total_hilang'] ?? 0) ?></h3>
                    <small class="text-white-50">Total Kasus</small>
                </div>
            </div>
        </div>
        <!-- Pending -->
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm bg-warning text-dark h-100">
                <div class="card-body py-3">
                    <h6 class="text-dark-50 mb-1 small text-uppercase fw-bold">Belum Selesai</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['total_pending'] ?? 0) ?></h3>
                    <small class="text-dark-50">Butuh Ganti Rugi</small>
                </div>
            </div>
        </div>
        <!-- Replaced -->
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm bg-success text-white h-100">
                <div class="card-body py-3">
                    <h6 class="text-white-50 mb-1 small text-uppercase fw-bold">Ganti Buku</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['total_replaced'] ?? 0) ?></h3>
                    <small class="text-white-50">Copy Fisik Baru</small>
                </div>
            </div>
        </div>
        <!-- Paid -->
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm bg-info text-white h-100">
                <div class="card-body py-3">
                    <h6 class="text-white-50 mb-1 small text-uppercase fw-bold">Ganti Uang</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['total_paid'] ?? 0) ?></h3>
                    <small class="text-white-50">Denda Finansial</small>
                </div>
            </div>
        </div>
        <!-- Total Denda Masuk -->
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm bg-dark text-white h-100">
                <div class="card-body py-3">
                    <h6 class="text-white-50 mb-1 small text-uppercase fw-bold text-success">Uang Masuk</h6>
                    <h3 class="mb-0 fw-bold text-success">Rp <?= number_format($stats['total_amount'] ?? 0, 0, ',', '.') ?></h3>
                    <small class="text-white-50">Total Kas Diterima</small>
                </div>
            </div>
        </div>
        <!-- Written Off -->
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm bg-secondary text-white h-100">
                <div class="card-body py-3">
                    <h6 class="text-white-50 mb-1 small text-uppercase fw-bold">Pemutihan</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['total_written_off'] ?? 0) ?></h3>
                    <small class="text-white-50">Dihapus Permanen</small>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light rounded-3 py-3">
            <form method="get" action="/lost-books" class="row g-2 align-items-end">
                <!-- Status -->
                <div class="col-md-3">
                    <label for="status" class="form-label small fw-bold text-secondary">Status Ganti Rugi</label>
                    <select name="status" id="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending (Belum Selesai)</option>
                        <option value="replaced" <?= $status === 'replaced' ? 'selected' : '' ?>>Ganti Buku Fisik</option>
                        <option value="paid" <?= $status === 'paid' ? 'selected' : '' ?>>Ganti Uang (Denda)</option>
                        <option value="written_off" <?= $status === 'written_off' ? 'selected' : '' ?>>Pemutihan</option>
                    </select>
                </div>
                <!-- Rentang Awal -->
                <div class="col-md-3">
                    <label for="start_date" class="form-label small fw-bold text-secondary">Dari Tanggal Hilang</label>
                    <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="<?= esc($start_date) ?>">
                </div>
                <!-- Rentang Akhir -->
                <div class="col-md-3">
                    <label for="end_date" class="form-label small fw-bold text-secondary">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="<?= esc($end_date) ?>">
                </div>
                <!-- Tombol Aksi -->
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold"><i class="fa-solid fa-filter"></i> Saring</button>
                    <a href="/lost-books" class="btn btn-sm btn-outline-secondary w-100 fw-semibold"><i class="fa-solid fa-arrow-rotate-left"></i> Reset</a>
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
                            <th class="ps-4" width="50">No</th>
                            <th width="120">Tgl Hilang</th>
                            <th width="140">Kode Transaksi</th>
                            <th width="180">Peminjam</th>
                            <th width="180">Buku / Kode Inv Asal</th>
                            <th width="130">Status Ganti</th>
                            <th width="110">Denda Uang</th>
                            <th width="240">Catatan Penyelesaian</th>
                            <th class="pe-4" width="130">Petugas Settle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lostBooks)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fa-2x mb-3 text-secondary"></i>
                                    <p class="mb-0">Tidak ada data buku hilang.</p>
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
                                    <td><?= $l['tanggal_hilang'] ? date('d/m/Y', strtotime($l['tanggal_hilang'])) : '-' ?></td>
                                    <td class="fw-bold text-dark"><?= esc($l['kode_transaksi']) ?></td>
                                    <td>
                                        <?= esc(preg_replace('/\s*\(siswa\)|\s*\(guru\)|\s*\(pegawai\)/i', '', $l['member_name'])) ?><br>
                                        <?php
                                        $badgeClass = 'bg-secondary';
                                        if ($l['jenis_pengguna'] === 'siswa') {
                                            $badgeClass = 'bg-success';
                                        } elseif ($l['jenis_pengguna'] === 'guru') {
                                            $badgeClass = 'bg-primary';
                                        } elseif ($l['jenis_pengguna'] === 'pegawai') {
                                            $badgeClass = 'bg-info';
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>" style="font-size: 0.72rem; text-transform: capitalize;">
                                            <?= esc($l['jenis_pengguna']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?= esc($l['judul']) ?></strong><br>
                                        <small class="text-muted">Inv: <code><?= esc($l['original_kode_inventaris'] ?: '-') ?></code></small>
                                    </td>
                                    <td>
                                        <?php if($l['lost_settlement_status'] === 'replaced'): ?>
                                            <span class="badge bg-success py-1.5 px-2 text-white">
                                                <i class="fa-solid fa-book me-1"></i> Ganti Buku
                                            </span>
                                        <?php elseif($l['lost_settlement_status'] === 'paid'): ?>
                                            <span class="badge bg-info py-1.5 px-2 text-white">
                                                <i class="fa-solid fa-money-bill-wave me-1"></i> Ganti Uang
                                            </span>
                                        <?php elseif($l['lost_settlement_status'] === 'written_off'): ?>
                                            <span class="badge bg-secondary py-1.5 px-2 text-white">
                                                <i class="fa-solid fa-trash-can me-1"></i> Pemutihan
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger py-1.5 px-2 text-white">
                                                <i class="fa-solid fa-clock me-1"></i> Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold <?= $l['lost_settlement_status'] === 'paid' ? 'text-dark' : 'text-muted' ?>">
                                        <?= $l['lost_settlement_status'] === 'paid' ? 'Rp '.number_format($l['lost_settlement_amount'], 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="small text-secondary" style="max-width: 240px; white-space: normal;">
                                        <?= esc($l['lost_note'] ?: '-') ?>
                                    </td>
                                    <td class="pe-4">
                                        <?php if($l['lost_settlement_status'] === 'pending' || empty($l['lost_settlement_status'])): ?>
                                            <button class="btn btn-sm btn-success text-nowrap fw-bold text-white d-inline-flex align-items-center gap-1 btn-settle"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#settleModal"
                                                    data-id="<?= $l['id'] ?>"
                                                    data-title="<?= esc($l['judul']) ?>"
                                                    data-inv="<?= esc($l['original_kode_inventaris'] ?: '-') ?>">
                                                <i class="fa-solid fa-check"></i> Settle / Ganti
                                            </button>
                                        <?php else: ?>
                                            <span class="text-success small fw-bold"><i class="fa-solid fa-circle-check"></i> <?= esc($l['verifier_name'] ?: 'Staff') ?></span><br>
                                            <small class="text-muted fs-8"><?= $l['settled_at'] ? date('d/m/y H:i', strtotime($l['settled_at'])) : '' ?></small>
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

<!-- MODAL SETTLEMENT -->
<div class="modal fade" id="settleModal" tabindex="-1" aria-labelledby="settleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="settleModalLabel">
                    <i class="fa-solid fa-check-to-slot me-2"></i> Penyelesaian Ganti Rugi Buku Hilang
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="settleForm" method="post" action="">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3 p-3 bg-light rounded-3 border-start border-4 border-success">
                        <span class="text-muted d-block small">Buku yang Hilang:</span>
                        <strong id="modalBookTitle" class="text-dark">Judul Buku</strong>
                        <span class="text-muted d-block small mt-1">Kode Inventaris: <code id="modalBookInv" class="fw-bold">Kode Inv</code></span>
                    </div>

                    <!-- Pilihan Tipe Penyelesaian -->
                    <div class="mb-3">
                        <label for="settlementType" class="form-label fw-bold small text-secondary">Metode Ganti Rugi <span class="text-danger">*</span></label>
                        <select name="settlement_type" id="settlementType" class="form-select" required>
                            <option value="replaced" selected>Ganti Buku Fisik (Menambahkan salinan baru & menambah stok)</option>
                            <option value="paid">Ganti Uang Tunai (Denda ganti rugi & mengurangi stok total)</option>
                            <option value="written_off">Pemutihan (Penghapusan aset tanpa ganti rugi & mengurangi stok total)</option>
                        </select>
                    </div>

                    <!-- Nominal Denda Ganti Rugi (Hanya muncul jika ganti uang) -->
                    <div class="mb-3 d-none" id="amountContainer">
                        <label for="settlementAmount" class="form-label fw-bold small text-secondary">Jumlah Denda Ganti Rugi (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="lost_settlement_amount" id="settlementAmount" class="form-control" placeholder="Contoh: 75000" min="0">
                        </div>
                        <small class="text-muted mt-1 d-block">Masukkan nominal uang yang dibayarkan oleh anggota.</small>
                    </div>

                    <!-- Catatan / Keterangan -->
                    <div class="mb-3">
                        <label for="settlementNote" class="form-label fw-bold small text-secondary">Catatan Penyelesaian <span class="text-danger">*</span></label>
                        <textarea name="lost_note" id="settlementNote" class="form-control" rows="3" placeholder="Tulis rincian ganti rugi..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-outline-secondary fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold text-white">Simpan Penyelesaian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const settleModal = document.getElementById('settleModal');
    const settleForm = document.getElementById('settleForm');
    const modalBookTitle = document.getElementById('modalBookTitle');
    const modalBookInv = document.getElementById('modalBookInv');
    const settlementType = document.getElementById('settlementType');
    const amountContainer = document.getElementById('amountContainer');
    const settlementAmount = document.getElementById('settlementAmount');
    const settlementNote = document.getElementById('settlementNote');

    // Menangani pembukaan modal dinamis
    const settleButtons = document.querySelectorAll('.btn-settle');
    settleButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const detailId = this.getAttribute('data-id');
            const title = this.getAttribute('data-title');
            const inv = this.getAttribute('data-inv');

            // Set Form action
            settleForm.setAttribute('action', `/lost-books/settle/${detailId}`);
            
            // Set text info
            modalBookTitle.textContent = title;
            modalBookInv.textContent = inv;

            // Reset modal inputs
            settlementType.value = 'replaced';
            amountContainer.classList.add('d-none');
            settlementAmount.removeAttribute('required');
            settlementAmount.value = '';
            settlementNote.value = 'Diganti dengan buku baru';
        });
    });

    // Menangani perubahan dropdown metode ganti rugi
    settlementType.addEventListener('change', function() {
        const val = this.value;
        if (val === 'paid') {
            amountContainer.classList.remove('d-none');
            settlementAmount.setAttribute('required', 'required');
            settlementNote.value = 'Denda uang ganti rugi dibayarkan lunas';
        } else {
            amountContainer.classList.add('d-none');
            settlementAmount.removeAttribute('required');
            settlementAmount.value = '';
            
            if (val === 'replaced') {
                settlementNote.value = 'Diganti dengan buku baru';
            } else if (val === 'written_off') {
                settlementNote.value = 'Diputihkan dari inventaris perpustakaan';
            }
        }
    });
});
</script>

<?= $this->endSection() ?>