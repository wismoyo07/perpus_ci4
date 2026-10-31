<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$rp = static fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
?>

<div class="container-fluid">

    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h4 class="text-dark fw-bold"><i class="fa-solid fa-money-bill-wave text-warning me-2"></i> Denda Keterlambatan</h4>
        <p class="text-muted mb-0">
            Denda dihitung <b>per buku</b> sejak H+1 jatuh tempo, maksimal <b><?= $maxDays > 0 ? $maxDays . ' hari' : 'tanpa batas' ?></b> (atur di Pengaturan).
            Member dengan denda <b>belum lunas</b> tidak dapat meminjam. Denda buku yang dilaporkan hilang <b>ditahan</b> sampai diselesaikan di menu Buku Hilang.
        </p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span><?= esc(session()->getFlashdata('success')) ?></span>
            <?php if (session()->getFlashdata('receipt_id')): ?>
                <a href="/fines/receipt/<?= (int) session()->getFlashdata('receipt_id') ?>" target="_blank" class="btn btn-sm btn-success fw-bold">
                    <i class="fa-solid fa-print"></i> Cetak Kwitansi
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <!-- RINGKASAN -->
    <div class="row g-3 mb-4">
        <?php foreach ([
            ['Belum Lunas', 'rp_belum', 'jml_belum', 'bg-danger text-white'],
            ['Ditahan (Buku Hilang)', 'rp_tahan', 'jml_tahan', 'bg-warning text-dark'],
            ['Lunas (Uang Masuk)', 'rp_lunas', 'jml_lunas', 'bg-success text-white'],
            ['Dibebaskan', 'rp_bebas', 'jml_bebas', 'bg-secondary text-white'],
        ] as [$label, $rpKey, $jmlKey, $cls]): ?>
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm <?= $cls ?> h-100">
                    <div class="card-body py-3">
                        <h6 class="mb-1 small text-uppercase fw-bold opacity-75"><?= $label ?></h6>
                        <h3 class="mb-0 fw-bold"><?= $rp($stats[$rpKey]) ?></h3>
                        <small class="opacity-75"><?= (int) $stats[$jmlKey] ?> buku</small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- FILTER -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light rounded-3 py-3">
            <form method="get" action="/fines" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary">Status Denda</label>
                    <select name="status" class="form-select form-select-sm">
                        <?php foreach (['belum_lunas' => 'Belum Lunas', 'ditahan' => 'Ditahan (Buku Hilang)', 'lunas' => 'Lunas', 'dibebaskan' => 'Dibebaskan', 'semua' => 'Semua Status'] as $val => $label): ?>
                            <option value="<?= $val ?>" <?= $status === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary">Cari Member / Transaksi / Judul / Kwitansi</label>
                    <input type="text" name="search" class="form-control form-control-sm" value="<?= esc($search) ?>" placeholder="Nama, NIS/NIP, TRX-..., judul, KW/...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-secondary">Tercatat Dari</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="<?= esc($start_date) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-secondary">Sampai</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="<?= esc($end_date) ?>">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold"><i class="fa-solid fa-filter"></i> Saring</button>
                    <a href="/fines" class="btn btn-sm btn-outline-secondary w-100"><i class="fa-solid fa-arrow-rotate-left"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL -->
    <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-4" width="40"><i class="fa-regular fa-square-check" title="Pilih untuk dibayar sekaligus"></i></th>
                            <th width="50">No</th>
                            <th width="150">Transaksi</th>
                            <th>Member</th>
                            <th>Buku</th>
                            <th width="190">Keterlambatan</th>
                            <th width="120" class="text-end">Denda</th>
                            <th width="110">Status</th>
                            <th width="200">Keterangan</th>
                            <th class="pe-4 text-end" width="110">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($fines)): ?>
                            <tr><td colspan="10" class="text-center text-muted py-4">Tidak ada data denda untuk filter ini.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($fines as $i => $f): ?>
                            <tr>
                                <td class="ps-4">
                                    <?php if ($f['status'] === 'belum_lunas'): ?>
                                        <input type="checkbox" class="form-check-input fine-pick" value="<?= $f['id'] ?>"
                                               data-user="<?= $f['user_id'] ?>" data-member="<?= esc($f['member_name']) ?>"
                                               data-jumlah="<?= (float) $f['jumlah'] ?>">
                                    <?php endif; ?>
                                </td>
                                <td><?= ($page - 1) * $perPage + $i + 1 ?></td>
                                <td><code class="fw-bold"><?= esc($f['kode_transaksi']) ?></code></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($f['member_name']) ?></div>
                                    <small class="text-muted"><?= esc($f['member_code']) ?> · <?= esc(ucfirst($f['jenis_pengguna'])) ?></small>
                                </td>
                                <td class="small">
                                    <?php if ($f['transaction_detail_id']): ?>
                                        <div class="fw-semibold text-dark"><?= esc($f['judul']) ?></div>
                                        <code class="text-muted"><?= esc($f['kode_inventaris']) ?></code>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">Denda lama (per transaksi)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small">
                                    <?php if ($f['transaction_detail_id']): ?>
                                        Jatuh tempo <?= date('d/m/Y', strtotime($f['jatuh_tempo'])) ?>
                                        <div class="text-danger fw-semibold">
                                            <?= (int) $f['hari_dikenakan'] ?> hari × <?= $rp($f['tarif']) ?>
                                            <?php if ((int) $f['hari_terlambat'] > (int) $f['hari_dikenakan']): ?>
                                                <span class="text-muted fw-normal">(terlambat <?= (int) $f['hari_terlambat'] ?> hari, dibatasi)</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($f['lost_reported_at']): ?>
                                            <div class="text-muted">Dilaporkan hilang <?= date('d/m/Y', strtotime($f['lost_reported_at'])) ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold text-danger">
                                    <?= $rp($f['jumlah']) ?>
                                    <?php if ((float) $f['jumlah'] !== (float) $f['jumlah_awal']): ?>
                                        <div class="small text-muted text-decoration-line-through fw-normal"><?= $rp($f['jumlah_awal']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= fine_status_badge($f['status']) ?></td>
                                <td class="small text-muted">
                                    <?php if ($f['diproses_at']): ?>
                                        <?= esc($f['petugas_name'] ?? '-') ?>, <?= date('d/m/Y H:i', strtotime($f['diproses_at'])) ?>
                                    <?php endif; ?>
                                    <?php if ($f['nomor_kwitansi']): ?><div>Kwitansi <code><?= esc($f['nomor_kwitansi']) ?></code></div><?php endif; ?>
                                    <?php if ($f['catatan']): ?><div class="fst-italic">"<?= esc($f['catatan']) ?>"</div><?php endif; ?>
                                    <?php if ($f['status'] === 'ditahan'): ?>
                                        <a href="/lost-books?status=pending">Selesaikan di Buku Hilang →</a>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <?php if ($f['status'] === 'belum_lunas'): ?>
                                        <button type="button" class="btn btn-sm btn-success fw-bold"
                                                data-bs-toggle="modal" data-bs-target="#fineModal"
                                                data-id="<?= $f['id'] ?>"
                                                data-kode="<?= esc($f['kode_transaksi']) ?>"
                                                data-member="<?= esc($f['member_name']) ?>"
                                                data-buku="<?= esc($f['judul'] ?? 'Denda lama (per transaksi)') ?>"
                                                data-denda="<?= esc($rp($f['jumlah'])) ?>">
                                            <i class="fa-solid fa-cash-register"></i> Proses
                                        </button>
                                    <?php elseif ($f['payment_id']): ?>
                                        <a href="/fines/receipt/<?= $f['payment_id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Cetak ulang kwitansi">
                                            <i class="fa-solid fa-print"></i> Kwitansi
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?= $pager ?>
</div>

<!-- BAR PEMBAYARAN GABUNGAN -->
<div id="payBar" class="position-fixed bottom-0 start-0 end-0 bg-white border-top shadow-lg py-2 d-none" style="z-index: 1030;">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <b id="payCount">0</b> denda dipilih untuk <b id="payMember"></b>, total <b class="text-danger" id="payTotal"></b>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="payClear">Batal Pilih</button>
            <button type="button" class="btn btn-sm btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#payModal">
                <i class="fa-solid fa-receipt"></i> Bayar &amp; Buat Kwitansi
            </button>
        </div>
    </div>
</div>

<!-- MODAL PEMBAYARAN GABUNGAN -->
<div class="modal fade" id="payModal" tabindex="-1" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="payModalLabel"><i class="fa-solid fa-receipt me-2"></i> Bayar Denda</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="payForm" method="post" action="/fines/pay">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p class="mb-2">Semua denda terpilih akan dicatat <b>LUNAS</b> dalam <b>satu kwitansi</b>.</p>
                    <div class="p-3 bg-light rounded mb-3">
                        <div class="fw-bold text-dark" id="payModalMember"></div>
                        <div class="small"><span id="payModalCount"></span> denda</div>
                        <div class="fs-5 fw-bold text-danger" id="payModalTotal"></div>
                    </div>
                    <label class="form-label fw-semibold">Catatan <span class="text-muted fw-normal">(opsional)</span></label>
                    <textarea name="catatan" class="form-control" rows="2" maxlength="255" placeholder="Misalnya: dibayar tunai oleh orang tua"></textarea>
                    <div id="payIds"></div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-outline-secondary fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold">Simpan &amp; Buat Kwitansi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL PROSES DENDA -->
<div class="modal fade" id="fineModal" tabindex="-1" aria-labelledby="fineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="fineModalLabel"><i class="fa-solid fa-cash-register me-2"></i> Proses Denda</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="fineForm" method="post" action="">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="p-3 bg-light rounded mb-3">
                        <div class="small text-muted">Transaksi <code id="fineKode" class="fw-bold"></code></div>
                        <div class="fw-bold text-dark" id="fineMember"></div>
                        <div class="small" id="fineBuku"></div>
                        <div class="fs-5 fw-bold text-danger" id="fineDenda"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Penyelesaian</label>
                        <select name="jenis" id="fineJenis" class="form-select" required>
                            <option value="lunas">Lunas (dibayar penuh, kwitansi dibuat)</option>
                            <option value="dibebaskan">Dibebaskan (diputihkan)</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold" id="fineCatatanLabel">Catatan <span class="text-muted fw-normal">(opsional)</span></label>
                        <textarea name="catatan" id="fineCatatan" class="form-control" rows="2" maxlength="255"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-outline-secondary fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal   = document.getElementById('fineModal');
    const form    = document.getElementById('fineForm');
    const jenis   = document.getElementById('fineJenis');
    const catatan = document.getElementById('fineCatatan');
    const label   = document.getElementById('fineCatatanLabel');

    function syncJenis() {
        const bebas = jenis.value === 'dibebaskan';
        catatan.required = bebas;
        catatan.placeholder = bebas ? 'Wajib: alasan pembebasan, misalnya nomor surat keputusan' : 'Misalnya: dibayar tunai ke petugas';
        label.innerHTML = bebas ? 'Alasan Pembebasan <span class="text-danger">*</span>' : 'Catatan <span class="text-muted fw-normal">(opsional)</span>';
    }

    modal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        form.action = '/fines/settle/' + btn.dataset.id;
        document.getElementById('fineKode').textContent   = btn.dataset.kode;
        document.getElementById('fineMember').textContent = btn.dataset.member;
        document.getElementById('fineBuku').textContent   = btn.dataset.buku;
        document.getElementById('fineDenda').textContent  = btn.dataset.denda;
        jenis.value = 'lunas';
        catatan.value = '';
        syncJenis();
    });

    jenis.addEventListener('change', syncJenis);

    // Pembayaran gabungan: hanya denda milik satu anggota dalam satu kwitansi
    const picks  = Array.from(document.querySelectorAll('.fine-pick'));
    const bar    = document.getElementById('payBar');
    const rupiah = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');

    function selected() { return picks.filter(p => p.checked); }

    function syncBar() {
        const sel   = selected();
        const total = sel.reduce((t, p) => t + parseFloat(p.dataset.jumlah), 0);
        bar.classList.toggle('d-none', sel.length === 0);
        if (!sel.length) return;
        document.getElementById('payCount').textContent  = sel.length;
        document.getElementById('payMember').textContent = sel[0].dataset.member;
        document.getElementById('payTotal').textContent  = rupiah(total);
        document.getElementById('payModalMember').textContent = sel[0].dataset.member;
        document.getElementById('payModalCount').textContent  = sel.length;
        document.getElementById('payModalTotal').textContent  = rupiah(total);
        document.getElementById('payIds').innerHTML = sel
            .map(p => '<input type="hidden" name="fine_ids[]" value="' + p.value + '">').join('');
    }

    picks.forEach(p => p.addEventListener('change', function () {
        const other = selected().find(x => x.dataset.user !== this.dataset.user);
        if (this.checked && other) {
            alert('Satu kwitansi hanya untuk denda milik satu anggota (' + other.dataset.member + ').');
            this.checked = false;
        }
        syncBar();
    }));

    document.getElementById('payClear').addEventListener('click', function () {
        picks.forEach(p => p.checked = false);
        syncBar();
    });
});
</script>

<?= $this->endSection() ?>
