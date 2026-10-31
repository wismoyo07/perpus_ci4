<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$rp = static fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
$tgl = static fn ($d) => $d ? date('d/m/Y', strtotime($d)) : '-';
$masihDipinjam = array_filter($details, static fn ($d) => $d['dipinjam']);   // belum dikembalikan & tidak hilang
$estimasiTotal = array_sum(array_column($masihDipinjam, 'denda_estimasi'));
$bisaDilaporkan = $today >= $hilangMulai;          // H+1 jatuh tempo sudah lewat
$dendaDini = $maxDays > 0 ? $maxDays * $tarif : 0;  // denda laporan dini (batas maksimal)
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Detail &amp; Pengembalian Peminjaman</h4>
        <a href="/transactions" class="btn btn-outline-secondary btn-sm">← Kembali</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <!-- INFO TRANSAKSI -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Kode:</strong> <?= esc($transaction['kode_transaksi']) ?></p>
                    <p class="mb-1"><strong>Peminjam:</strong> <?= esc($member['name']) ?> (<?= esc(ucfirst($member['jenis_pengguna'])) ?>)</p>
                    <p class="mb-1"><strong>Tanggal Pinjam:</strong> <?= $tgl($transaction['tanggal_pinjam']) ?></p>
                    <p class="mb-0"><strong>Jatuh Tempo:</strong> <?= $tgl($transaction['jatuh_tempo']) ?></p>
                </div>
                <div class="col-md-6 small text-muted">
                    Tarif: <b><?= $rp($tarif) ?></b> per buku per hari, dihitung sejak H+1 jatuh tempo,
                    maksimal <b><?= $maxDays > 0 ? $maxDays . ' hari' : 'tanpa batas' ?></b> per buku.
                </div>
            </div>
            <?php if ($terlambat > 0): ?>
                <div class="alert alert-danger mt-3 mb-0">
                    Status: <b>TERLAMBAT <?= $terlambat ?> hari</b>.
                    Perkiraan denda bila dikembalikan hari ini: <b><?= $rp($estimasiTotal) ?></b>
                    <?php if ($maxDays > 0 && $terlambat > $maxDays): ?>(dibatasi <?= $maxDays ?> hari per buku)<?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- DAFTAR BUKU -->
    <div class="card shadow-sm mb-4">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="table-light small text-uppercase text-muted">
                    <tr>
                        <th class="ps-3">Buku</th>
                        <th width="260">Status</th>
                        <th width="220" class="text-end pe-3">Denda</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($details as $d): ?>
                        <tr>
                            <td class="ps-3">
                                <strong><?= esc($d['judul']) ?></strong><br>
                                <small class="text-muted">Kode Inv: <?= esc($d['kode_inventaris'] ?: '-') ?></small>
                            </td>
                            <td>
                                <?php if ($d['dipinjam']): ?>
                                    <span class="badge <?= $terlambat > 0 ? 'bg-danger' : 'bg-primary' ?>"><?= $terlambat > 0 ? 'Belum Kembali (Terlambat)' : 'Dipinjam' ?></span>
                                <?php elseif (!$d['is_lost']): ?>
                                    <span class="badge bg-success">Dikembalikan</span>
                                    <div class="small text-muted"><?= $tgl($d['returned_at']) ?></div>
                                <?php elseif ($d['lost_settlement_status'] === 'found'): ?>
                                    <span class="badge bg-success">Hilang → Ditemukan</span>
                                    <div class="small text-muted">Dilaporkan <?= $tgl($d['lost_reported_at']) ?></div>
                                <?php else: ?>
                                    <span class="badge bg-danger">Dilaporkan Hilang</span>
                                    <div class="small text-muted">
                                        Per <?= $tgl($d['lost_reported_at']) ?>
                                        <?= $d['lost_settlement_status'] !== 'pending' ? '· sudah diselesaikan' : '· menunggu penyelesaian' ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-end small pe-3">
                                <?php if ($d['dipinjam']): ?>
                                    <?php if ($d['denda_estimasi'] > 0): ?>
                                        <span class="text-danger fw-semibold">± <?= $rp($d['denda_estimasi']) ?></span>
                                        <div class="text-muted"><?= $d['hari_estimasi'] ?> hari, terus berjalan (perkiraan)</div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                <?php elseif ($d['denda_ditahan'] !== null): ?>
                                    <span class="fw-semibold"><?= $rp($d['denda_ditahan']) ?></span>
                                    <div class="text-muted"><?= (int) $d['hari_dikenakan'] ?> hari · <?= esc(str_replace('_', ' ', $d['denda_status'])) ?></div>
                                <?php elseif ($d['is_lost']): ?>
                                    <span class="text-muted">Tidak ada (dilaporkan sebelum terlambat)</span>
                                <?php else: ?>
                                    <span class="text-muted">Tepat waktu</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-4">
        <!-- LAPORKAN HILANG -->
        <div class="col-lg-5">
            <div class="card shadow-sm h-100 border-danger">
                <div class="card-header bg-danger text-white fw-semibold">Laporkan Buku Hilang</div>
                <div class="card-body">
                    <?php if (empty($masihDipinjam)): ?>
                        <p class="text-muted mb-0">Tidak ada buku yang masih dipinjam pada transaksi ini.</p>
                    <?php else: ?>
                        <form method="post" id="reportLostForm" action="">
                            <?= csrf_field() ?>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Buku</label>
                                <select id="reportLostItem" class="form-select" required>
                                    <?php foreach ($masihDipinjam as $d): ?>
                                        <option value="<?= $d['id'] ?>"><?= esc($d['judul']) ?> (<?= esc($d['kode_inventaris'] ?: '-') ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Tanggal laporan peminjam</label>
                                <input type="date" name="tanggal_lapor" id="tanggalLapor" class="form-control" required
                                       value="<?= $bisaDilaporkan ? $today : $hilangMulai ?>"
                                       min="<?= $hilangMulai ?>" max="<?= $today ?>"
                                       data-min-normal="<?= $hilangMulai ?>" data-min-dini="<?= $transaction['tanggal_pinjam'] ?>">
                                <small class="text-muted">Buku bisa dinyatakan hilang mulai <b><?= $tgl($hilangMulai) ?></b> (H+1 jatuh tempo).</small>
                            </div>

                            <?php if ($dendaDini > 0): ?>
                                <div class="form-check mb-2 p-2 ps-4 border border-warning rounded bg-warning-subtle">
                                    <input class="form-check-input" type="checkbox" name="laporan_dini" value="1" id="laporanDini" <?= $bisaDilaporkan ? '' : 'required' ?>>
                                    <label class="form-check-label small" for="laporanDini">
                                        <b>Laporan dini</b> (dicatat sebelum <?= $tgl($hilangMulai) ?>):
                                        langsung dikenakan denda batas maksimal <b><?= $maxDays ?> hari × <?= $rp($tarif) ?> = <?= $rp($dendaDini) ?></b>,
                                        langsung ditagih, dan <b>tetap berlaku</b> walaupun buku nanti ditemukan atau diganti.
                                    </label>
                                </div>
                                <div class="mb-2 d-none" id="alasanDiniGroup">
                                    <textarea name="alasan_dini" id="alasanDini" class="form-control" rows="2" maxlength="200" placeholder="Wajib: alasan laporan dini"></textarea>
                                </div>
                            <?php elseif (!$bisaDilaporkan): ?>
                                <p class="small text-danger">Laporan dini tidak tersedia karena batas maksimal hari denda di Pengaturan diisi 0 (tanpa batas).</p>
                            <?php endif; ?>

                            <p class="small text-muted">Denda buku ini berhenti bertambah di tanggal laporan dan <b>ditahan</b> sampai ada penyelesaian di menu Buku Hilang (ditemukan, diganti, atau pemutihan).</p>
                            <button type="submit" class="btn btn-danger w-100" <?= (!$bisaDilaporkan && $dendaDini === 0) ? 'disabled' : '' ?> onclick="return confirm('Laporkan buku ini hilang?')">Laporkan Hilang</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- PROSES PENGEMBALIAN -->
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">Proses Pengembalian</div>
                <div class="card-body">
                    <form method="post" action="/transactions/return/<?= $transaction['id'] ?>">
                        <?= csrf_field() ?>

                        <?php if (empty($masihDipinjam)): ?>
                            <p class="small text-muted">Semua buku sudah dikembalikan atau dilaporkan hilang. Klik proses untuk menutup transaksi.</p>
                        <?php else: ?>
                            <p class="small text-muted mb-2">Pilih aksi tiap buku. Buku yang <b>belum dibawa</b> tetap dipinjam dan dendanya terus berjalan sampai dikembalikan.</p>
                            <table class="table table-sm align-middle mb-2">
                                <tbody>
                                    <?php foreach ($masihDipinjam as $d): ?>
                                        <tr>
                                            <td class="small">
                                                <strong><?= esc($d['judul']) ?></strong><br>
                                                <span class="text-muted"><?= esc($d['kode_inventaris'] ?: '-') ?></span>
                                            </td>
                                            <td width="210">
                                                <select name="aksi[<?= $d['book_item_id'] ?>]" class="form-select form-select-sm">
                                                    <option value="kembali" selected>Dikembalikan sekarang</option>
                                                    <option value="">Belum dibawa (tetap dipinjam)</option>
                                                    <?php if ($bisaDilaporkan): ?>
                                                        <option value="hilang">Hilang (lapor hari ini)</option>
                                                    <?php endif; ?>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if (!$bisaDilaporkan): ?>
                                <p class="small text-muted">Pilihan <b>Hilang</b> tersedia mulai <?= $tgl($hilangMulai) ?> (H+1 jatuh tempo). Sebelum itu gunakan <b>Laporkan Hilang</b> dengan opsi laporan dini.</p>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="alert alert-info small mt-2">
                            Buku yang dikembalikan dikenakan denda per buku sesuai keterlambatan (maksimal <?= $maxDays > 0 ? $maxDays . ' hari' : 'tanpa batas' ?>).
                            Buku hilang: denda berhenti di tanggal laporan dan ditahan sampai diselesaikan
                            (ditemukan → tetap dibayar; diganti buku/uang → dihapus; pemutihan → sesuai surat keputusan).
                        </div>

                        <button type="submit" class="btn btn-primary">Proses Pengembalian</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('reportLostForm');
    const item = document.getElementById('reportLostItem');
    if (form && item) {
        const sync = () => { form.action = '/transactions/report-lost/' + item.value; };
        item.addEventListener('change', sync);
        sync();
    }

    // Laporan dini: tanggal boleh sebelum H+1, alasan wajib
    const dini = document.getElementById('laporanDini');
    const tanggal = document.getElementById('tanggalLapor');
    const alasanGroup = document.getElementById('alasanDiniGroup');
    const alasan = document.getElementById('alasanDini');
    if (dini && tanggal) {
        const syncDini = () => {
            tanggal.min = dini.checked ? tanggal.dataset.minDini : tanggal.dataset.minNormal;
            alasanGroup.classList.toggle('d-none', !dini.checked);
            alasan.required = dini.checked;
        };
        dini.addEventListener('change', syncDini);
        syncDini();
    }
});
</script>

<?= $this->endSection() ?>
