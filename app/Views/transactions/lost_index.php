<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="mb-4">
        <h4 class="text-dark fw-bold"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i> Tracking Buku Hilang & Ganti Rugi</h4>
        <p class="text-muted">Pantau status penggantian buku hilang dan ganti rugi fisik.</p>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm mb-3">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-3">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-muted">
                        <tr>
                            <th class="ps-4" width="60">No</th>
                            <th>Kode Transaksi</th>
                            <th>Peminjam</th>
                            <th>Buku / Kode Inventaris</th>
                            <th>Tgl Pinjam</th>
                            <th>Status</th>
                            <th>Catatan Penyelesaian</th>
                            <th class="pe-4 text-end" style="min-width: 250px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lostBooks)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Tidak ada data buku hilang.</td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $perPage = 10;
                            $i = ($page - 1) * $perPage + 1;
                            ?>
                            <?php foreach ($lostBooks as $l): ?>
                                <tr>
                                    <td class="ps-4 text-muted"><?= $i++ ?></td>
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
                                        <span class="badge <?= $badgeClass ?>" style="font-size: 0.75rem; text-transform: capitalize;">
                                            <?= esc($l['jenis_pengguna']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?= esc($l['judul']) ?></strong><br>
                                        <small class="text-muted">Inv Asal: <code><?= esc($l['original_kode_inventaris'] ?: '-') ?></code></small>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($l['tanggal_pinjam'])) ?></td>
                                    <td>
                                        <?php if($l['lost_settlement_status'] === 'replaced'): ?>
                                            <span class="badge bg-success">Selesai (Diganti)</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Pending (Belum Diganti)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-secondary" style="max-width: 280px; white-space: normal;">
                                        <?= esc($l['lost_note'] ?: '-') ?>
                                    </td>
                                    <td class="pe-4">
                                        <div class="d-flex justify-content-end">
                                            <?php if($l['lost_settlement_status'] !== 'replaced'): ?>
                                                <form method="post" 
                                                      action="/lost-books/settle/<?= $l['id'] ?>" 
                                                      class="d-flex gap-1 align-items-center mb-0 w-100"
                                                      style="max-width: 320px;">
                                                    <?= csrf_field() ?>
                                                    <input type="text" 
                                                           name="lost_note" 
                                                           class="form-control form-control-sm" 
                                                           placeholder="Catatan penggantian..." 
                                                           required>
                                                    <button type="submit" class="btn btn-sm btn-success text-nowrap fw-bold text-white d-flex align-items-center gap-1">
                                                        <i class="fa-solid fa-check"></i> Settle / Ganti
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-success small fw-bold"><i class="fa-solid fa-circle-check"></i> Diselesaikan</span>
                                            <?php endif; ?>
                                        </div>
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