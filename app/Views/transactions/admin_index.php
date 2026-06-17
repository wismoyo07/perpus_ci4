<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-clipboard-data text-primary"></i> Monitoring Transaksi (Admin View)</h4>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Kode</th>
                            <th>Member</th>
                            <th>Tipe Pinjam</th>
                            <th>Tgl Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Denda</th>
                            <th class="pe-4">Staff / Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada transaksi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $trx): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark"><?= esc($trx['kode_transaksi']) ?></td>
                                    <td>
                                        <?= esc($trx['member_name']) ?><br>
                                        <span class="badge bg-secondary" style="font-size: 0.75rem;">
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
                                        <?php if ($trx['status'] === 'dipinjam'): ?>
                                            <span class="badge bg-warning text-dark">Dipinjam</span>
                                        <?php elseif ($trx['status'] === 'terlambat'): ?>
                                            <span class="badge bg-danger">Terlambat</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-medium text-danger">Rp <?= number_format($trx['total_denda'], 0, ',', '.') ?></td>
                                    <td class="pe-4">
                                        <small class="text-muted">
                                            Pinjam: <?= esc($trx['staff_borrow']) ?><br>
                                            <?php if ($trx['staff_return']): ?>
                                                Kembali: <?= esc($trx['staff_return']) ?>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <?= $pager->links() ?>
    </div>

</div>

<?= $this->endSection() ?>