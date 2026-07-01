<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Daftar Transaksi</h4>
        <a href="/transactions/create" class="btn btn-primary btn-sm">
            + Transaksi Baru
        </a>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Filter & Search Card Start -->
    <div class="card shadow-sm border-0 mb-4 rounded-3">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-filter text-success me-2"></i> Penapisan Transaksi</h5>
        </div>
        <div class="card-body">
            <form method="get" action="" class="row g-3">
                <!-- Row 1 -->
                <div class="col-md-4">
                    <label for="search" class="form-label fw-bold text-muted small">Kata Kunci Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" id="search" class="form-control border-start-0 ps-0" placeholder="Kode trx, nama anggota, staff..." value="<?= esc($search ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="jenis_pinjam" class="form-label fw-bold text-muted small">Tipe Pinjam</label>
                    <select name="jenis_pinjam" id="jenis_pinjam" class="form-select">
                        <option value="" <?= ($jenis_pinjam === '') ? 'selected' : '' ?>>Semua Tipe</option>
                        <option value="pendek" <?= ($jenis_pinjam === 'pendek') ? 'selected' : '' ?>>Jangka Pendek</option>
                        <option value="panjang" <?= ($jenis_pinjam === 'panjang') ? 'selected' : '' ?>>Jangka Panjang</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label fw-bold text-muted small">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="" <?= ($status === '') ? 'selected' : '' ?>>Semua Status</option>
                        <option value="dipinjam" <?= ($status === 'dipinjam') ? 'selected' : '' ?>>Dipinjam</option>
                        <option value="terlambat" <?= ($status === 'terlambat') ? 'selected' : '' ?>>Terlambat</option>
                        <option value="selesai" <?= ($status === 'selesai') ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>

                <!-- Row 2 -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small d-block mb-1">Rentang Tanggal Pinjam</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="date" name="start_pinjam" class="form-control" value="<?= esc($start_pinjam ?? '') ?>" placeholder="Mulai">
                        </div>
                        <div class="col-6">
                            <input type="date" name="end_pinjam" class="form-control" value="<?= esc($end_pinjam ?? '') ?>" placeholder="Sampai">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small d-block mb-1">Rentang Tanggal Kembali</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="date" name="start_kembali" class="form-control" value="<?= esc($start_kembali ?? '') ?>" placeholder="Mulai">
                        </div>
                        <div class="col-6">
                            <input type="date" name="end_kembali" class="form-control" value="<?= esc($end_kembali ?? '') ?>" placeholder="Sampai">
                        </div>
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn btn-success px-4 fw-bold d-flex align-items-center gap-1">
                        <i class="fa-solid fa-magnifying-glass"></i> Filter Data
                    </button>
                    <a href="?" class="btn btn-outline-secondary px-4 fw-bold d-flex align-items-center gap-1">
                        <i class="fa-solid fa-arrows-rotate"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    <!-- Filter & Search Card End -->

    <!-- Transactions Table Card Start -->
    <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th width="60">No</th>
                <th>Kode</th>
                <th>Nama Anggota</th>
                <th>Tipe Pinjam</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Tgl Kembali / Status</th>
                <th>Denda</th>
                <th>Staff</th>
                <th width="150">Aksi</th>
            </tr>
        </thead>
        
        <tbody>
            <?php 
            $page = $pager->getCurrentPage();
            $perPage = 10;
            $i = ($page - 1) * $perPage + 1;
            ?>
            <?php foreach ($transactions as $trx): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td>
                    <?php if ($trx['status'] === 'selesai'): ?>
                        <a href="#" data-bs-toggle="collapse" data-bs-target="#books-<?= $trx['id'] ?>" class="text-success fw-bold text-decoration-none d-inline-flex align-items-center gap-1">
                            <i class="fa-solid fa-circle-chevron-down"></i> <?= esc($trx['kode_transaksi']) ?>
                        </a>
                    <?php else: ?>
                        <?= esc($trx['kode_transaksi']) ?>
                    <?php endif; ?>
                </td>

                <td>
                    <?= esc(preg_replace('/\s*\(siswa\)|\s*\(guru\)|\s*\(pegawai\)/i', '', $trx['member_name'])) ?><br>
                    <?php
                    $badgeClass = 'bg-secondary';
                    if ($trx['jenis_pengguna'] === 'siswa') {
                        $badgeClass = 'bg-success';
                    } elseif ($trx['jenis_pengguna'] === 'guru') {
                        $badgeClass = 'bg-primary';
                    } elseif ($trx['jenis_pengguna'] === 'pegawai') {
                        $badgeClass = 'bg-info';
                    }
                    ?>
                    <span class="badge <?= $badgeClass ?>" style="font-size: 0.75rem; text-transform: capitalize;">
                        <?= esc($trx['jenis_pengguna']) ?>
                    </span>
                </td>

                <td>
                    <?php if(($trx['jenis_peminjaman'] ?? 'pendek') === 'panjang'): ?>
                        <span class="badge bg-dark">Jangka Panjang</span>
                    <?php else: ?>
                        <span class="badge bg-info text-white">Jangka Pendek</span>
                    <?php endif; ?>
                </td>

                <td><?= date('d/m/Y', strtotime($trx['tanggal_pinjam'])) ?></td>
                <td><?= date('d/m/Y', strtotime($trx['jatuh_tempo'])) ?></td>

                <td>
                    <?php if ($trx['status'] === 'selesai'): ?>
                        <?php if ($trx['tanggal_kembali']): ?>
                            <div class="fw-bold text-dark mb-1" style="font-size: 0.85rem;"><?= date('d/m/Y', strtotime($trx['tanggal_kembali'])) ?></div>
                        <?php else: ?>
                            <div class="text-muted small mb-1">-</div>
                        <?php endif; ?>
                        <span class="badge bg-success">Selesai</span>
                    <?php else: ?>
                        <?php if ($trx['status'] === 'dipinjam'): ?>
                            <span class="badge bg-primary">Dipinjam</span>
                        <?php elseif ($trx['status'] === 'terlambat'): ?>
                            <span class="badge bg-danger">Terlambat</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>

                <td>Rp <?= number_format($trx['total_denda'],0,',','.') ?></td>

                <td>
                    <small>
                        Pinjam: <?= esc($trx['staff_borrow']) ?><br>
                        <?php if($trx['staff_return']): ?>
                            Kembali: <?= esc($trx['staff_return']) ?>
                        <?php endif; ?>
                    </small>
                </td>

                <td>
                    <?php if($trx['status'] !== 'selesai'): ?>
                        <a href="/transactions/return/<?= $trx['id'] ?>"
                        class="btn btn-sm btn-warning">
                        Return
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if ($trx['status'] === 'selesai'): ?>
                <tr class="collapse bg-light" id="books-<?= $trx['id'] ?>">
                    <td colspan="10" class="p-3">
                        <div class="card border-0 shadow-none bg-white rounded-3 p-3">
                            <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-book text-success me-2"></i> Daftar Buku yang Dikembalikan:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0" style="white-space: normal !important;">
                                    <thead class="table-light text-uppercase fs-8 text-muted">
                                        <tr>
                                            <th width="180">Kode Buku / Inv</th>
                                            <th>Judul</th>
                                            <th>Pengarang</th>
                                            <th>Penerbit</th>
                                            <th width="120">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($trx['details'] ?? [] as $det): ?>
                                            <tr>
                                                <td class="fw-bold text-success">[<?= esc($det['kode_inventaris'] ?: $det['kode_buku'] ?: '-') ?>]</td>
                                                <td><strong><?= esc($det['judul']) ?></strong></td>
                                                <td><?= esc($det['pengarang'] ?: '-') ?></td>
                                                <td><?= esc($det['penerbit'] ?: '-') ?></td>
                                                <td>
                                                    <?php if ($det['is_lost']): ?>
                                                        <span class="badge bg-danger">Hilang</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Kembali</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
            </table>
        </div>
    </div>
</div>

    <div class="d-flex justify-content-center mt-3">
        <?= $pager->links('default', 'notika') ?>
    </div>

</div>

<?= $this->endSection() ?>