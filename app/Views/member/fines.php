<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php $rp = static fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.'); ?>

<div class="container-fluid">

    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h4 class="text-dark fw-bold"><i class="fa-solid fa-money-bill-wave text-warning me-2"></i> Denda Saya</h4>
        <p class="text-muted mb-0">Denda dihitung per buku sejak H+1 jatuh tempo. Pembayaran dilakukan di petugas perpustakaan, dan Anda akan menerima kwitansi.</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?php if ((int) $totals['jml_belum'] > 0): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-ban me-1"></i>
            Anda memiliki <b><?= (int) $totals['jml_belum'] ?> denda belum lunas</b> sebesar <b><?= $rp($totals['rp_belum']) ?></b>.
            Selama denda belum dilunasi, Anda <b>tidak dapat meminjam buku</b>. Silakan bayar di petugas perpustakaan.
        </div>
    <?php endif; ?>
    <?php if ($lateRunning > 0): ?>
        <div class="alert alert-warning">
            <i class="fa-solid fa-hourglass-half me-1"></i>
            <?= $lateRunning ?> pinjaman sudah melewati jatuh tempo. Dendanya masih berjalan dan baru tercatat di sini saat buku dikembalikan.
        </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <?php foreach ([
            ['Belum Lunas', 'rp_belum', 'jml_belum', 'bg-danger text-white'],
            ['Ditahan (Buku Hilang)', 'rp_tahan', 'jml_tahan', 'bg-warning text-dark'],
            ['Sudah Dibayar', 'rp_lunas', 'jml_lunas', 'bg-success text-white'],
            ['Dibebaskan', 'rp_bebas', 'jml_bebas', 'bg-secondary text-white'],
        ] as [$label, $rpKey, $jmlKey, $cls]): ?>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm <?= $cls ?> h-100">
                    <div class="card-body py-3">
                        <h6 class="mb-1 small text-uppercase fw-bold opacity-75"><?= $label ?></h6>
                        <h4 class="mb-0 fw-bold"><?= $rp($totals[$rpKey]) ?></h4>
                        <small class="opacity-75"><?= (int) $totals[$jmlKey] ?> buku</small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-4">Buku</th>
                            <th>Keterangan</th>
                            <th class="text-end">Denda</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Kwitansi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($fines)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Anda tidak memiliki denda. Terima kasih sudah mengembalikan buku tepat waktu.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($fines as $f): ?>
                            <tr>
                                <td class="ps-4 small">
                                    <?php if ($f['transaction_detail_id']): ?>
                                        <div class="fw-semibold text-dark"><?= esc($f['judul']) ?></div>
                                        <code class="text-muted"><?= esc($f['kode_inventaris']) ?></code>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">Denda lama (per transaksi)</span>
                                    <?php endif; ?>
                                    <div class="text-muted"><?= esc($f['kode_transaksi']) ?></div>
                                </td>
                                <td class="small">
                                    <?php if ($f['transaction_detail_id']): ?>
                                        Jatuh tempo <?= date('d/m/Y', strtotime($f['jatuh_tempo'])) ?>
                                        <div><?= $f['is_lost'] ? 'Buku hilang' : 'Terlambat' ?>: <?= (int) $f['hari_dikenakan'] ?> hari × <?= $rp($f['tarif']) ?></div>
                                    <?php endif; ?>
                                    <?php if ($f['status'] === 'ditahan'): ?>
                                        <div class="text-muted">Menunggu penyelesaian buku hilang di petugas.</div>
                                    <?php endif; ?>
                                    <?php if ($f['status'] === 'dibebaskan' && $f['catatan']): ?>
                                        <div class="fst-italic text-muted">"<?= esc($f['catatan']) ?>"</div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold text-danger">
                                    <?= $rp($f['jumlah']) ?>
                                    <?php if ((float) $f['jumlah'] !== (float) $f['jumlah_awal']): ?>
                                        <div class="small text-muted text-decoration-line-through fw-normal"><?= $rp($f['jumlah_awal']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= fine_status_badge($f['status']) ?></td>
                                <td class="pe-4 text-end">
                                    <?php if ($f['payment_id']): ?>
                                        <a href="/member/fines/receipt/<?= $f['payment_id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa-solid fa-print"></i> <?= esc($f['nomor_kwitansi']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
