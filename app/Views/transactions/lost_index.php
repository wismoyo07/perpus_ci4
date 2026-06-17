<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-exclamation-triangle-fill text-danger"></i> Tracking Buku Hilang & Ganti Rugi</h4>
        <a href="/transactions" class="btn btn-outline-secondary btn-sm">← Kembali</a>
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

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Kode Transaksi</th>
                            <th>Peminjam (Member)</th>
                            <th>Buku / Kode Inventaris</th>
                            <th>Tgl Pinjam</th>
                            <th>Status</th>
                            <th>Catatan Penyelesaian</th>
                            <th class="pe-4" style="min-width: 250px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lostBooks)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Tidak ada data buku hilang.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($lostBooks as $l): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark"><?= esc($l['kode_transaksi']) ?></td>
                                    <td><?= esc($l['member_name']) ?></td>
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
                                        <?php if($l['lost_settlement_status'] !== 'replaced'): ?>
                                            <form method="post" 
                                                  action="/lost-books/settle/<?= $l['id'] ?>" 
                                                  class="d-flex gap-1 align-items-center">
                                                <?= csrf_field() ?>
                                                <input type="text" 
                                                       name="lost_note" 
                                                       class="form-control form-control-sm" 
                                                       placeholder="Catatan penggantian..." 
                                                       required>
                                                <button type="submit" class="btn btn-sm btn-primary text-nowrap">
                                                    Settle / Ganti
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small"><i class="bi bi-check-circle-fill text-success"></i> Diselesaikan</span>
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

</div>

<?= $this->endSection() ?>