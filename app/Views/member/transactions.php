<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h4>Riwayat Peminjaman Saya</h4>

<!-- Alert Transaksi Member -------------- -->
<?php if($lostPending > 0): ?>
<div class="alert alert-danger">
    🔴 Anda memiliki <?= $lostPending ?> buku hilang belum diganti.
</div>
<?php endif; ?>

<?php if($lateCount > 0): ?>
<div class="alert alert-danger">
    ⚠ Anda memiliki <?= $lateCount ?> transaksi terlambat.
</div>
<?php endif; ?>

<?php if($dueSoon > 0): ?>
<div class="alert alert-warning">
    ⏳ <?= $dueSoon ?> transaksi mendekati jatuh tempo (≤ 3 hari).
</div>
<?php endif; ?>

<div class="accordion" id="trxAccordion">

<?php foreach ($transactions as $index => $trx): ?>

<?php 
$hasLostPending = false;
foreach ($trx['details'] ?? [] as $d) {
    if ($d['is_lost'] && $d['lost_settlement_status'] === 'pending') {
        $hasLostPending = true;
    }
}
?>

<?php if($dueSoon > 0): ?>
    <div class="alert alert-warning">
        ⏳ Anda memiliki <?= $dueSoon ?> transaksi mendekati jatuh tempo (H-2).
    </div>
<?php endif; ?>

    <div class="accordion-item mb-2 
        <?= $hasLostPending ? 'border border-danger' : '' ?>">

        <h2 class="accordion-header" id="heading<?= $index ?>">
            <button class="accordion-button collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapse<?= $index ?>">

                    
                <?= esc($trx['kode_transaksi']) ?> |
                <?= $trx['tanggal_pinjam'] ?> → <?= $trx['jatuh_tempo'] ?>
                <?php if($trx['status'] === 'dipinjam'): ?>
                    <span class="badge bg-primary ms-2">Aktif</span>
                <?php elseif($trx['status'] === 'terlambat'): ?>
                    <span class="badge bg-danger ms-2">Terlambat</span>
                <?php endif; ?>

                <?php if($trx['status'] === 'terlambat'): ?>
                    <span class="badge bg-danger ms-2">Terlambat</span>
                <?php elseif($hasLostPending): ?>
                    <span class="badge bg-danger ms-2">Lost Pending</span>
                <?php endif; ?>
            </button>
        </h2>

        <div id="collapse<?= $index ?>"
            class="accordion-collapse collapse"
            data-bs-parent="#trxAccordion">

            <div class="accordion-body">

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($trx['details'] as $d): ?>
                        <tr>
                            <td><?= esc($d['judul']) ?></td>
                            <td>
                                <?php if($d['is_lost']): ?>

                                    <?php if($d['lost_settlement_status'] === 'replaced'): ?>
                                        <span class="badge bg-success">
                                            Hilang (Diganti Buku)
                                        </span>
                                    <?php elseif($d['lost_settlement_status'] === 'paid'): ?>
                                        <span class="badge bg-info text-white">
                                            Hilang (Denda Lunas)
                                        </span>
                                    <?php elseif($d['lost_settlement_status'] === 'written_off'): ?>
                                        <span class="badge bg-secondary">
                                            Hilang (Pemutihan)
                                        </span>
                                    <?php elseif($d['lost_settlement_status'] === 'found'): ?>
                                        <span class="badge bg-primary">
                                            Hilang (Ditemukan)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            Hilang (Belum Diganti)
                                        </span>
                                    <?php endif; ?>

                                <?php elseif (!empty($d['returned_at'])): ?>

                                    <span class="badge bg-secondary">
                                        Dikembalikan <?= date('d/m/Y', strtotime($d['returned_at'])) ?>
                                    </span>

                                <?php else: ?>

                                    <?php if($trx['status'] === 'dipinjam'): ?>
                                        <span class="badge bg-primary">
                                            Sedang Dipinjam
                                        </span>

                                    <?php elseif($trx['status'] === 'terlambat'): ?>
                                        <span class="badge bg-danger">
                                            Terlambat
                                        </span>

                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            Dikembalikan
                                        </span>
                                    <?php endif; ?>

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>

                <div>
                    <strong>Denda:</strong>
                    Rp <?= number_format($trx['total_denda'],0,',','.') ?>
                </div>

            </div>
        </div>
    </div>

<?php endforeach; ?>

</div>

<?= $this->endSection() ?>