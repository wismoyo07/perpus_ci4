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
                            <th class="ps-4" width="60">No</th>
                            <th>Kode</th>
                            <th>Nama Anggota</th>
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
                                <td colspan="9" class="text-center py-4 text-muted">Belum ada transaksi.</td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $page = $pager->getCurrentPage();
                            $perPage = 10;
                            $i = ($page - 1) * $perPage + 1;
                            ?>
                            <?php foreach ($transactions as $trx): ?>
                                <tr>
                                    <td class="ps-4 text-muted"><?= $i++ ?></td>
                                    <td class="fw-bold text-dark"><?= esc($trx['kode_transaksi']) ?></td>
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

    <div class="d-flex justify-content-center mt-4">
        <?= $pager->links('default', 'notika') ?>
    </div>

</div>

<?= $this->endSection() ?>