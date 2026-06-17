<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Proses Pengembalian</h4>
        <a href="/transactions" class="btn btn-outline-secondary btn-sm">
            ← Kembali
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <p><strong>Kode:</strong> <?= esc($transaction['kode_transaksi']) ?></p>
            <p><strong>Tanggal Pinjam:</strong> <?= $transaction['tanggal_pinjam'] ?></p>
            <p><strong>Jatuh Tempo:</strong> <?= $transaction['jatuh_tempo'] ?></p>

            <?php if($transaction['status'] === 'terlambat'): ?>
                <div class="alert alert-danger">
                    Status: TERLAMBAT
                </div>
            <?php endif; ?>

        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="post"
                  action="/transactions/return/<?= $transaction['id'] ?>">

                <?= csrf_field() ?>

                <h6>Pilih Buku Hilang (jika ada)</h6>

                <div class="row">

                    <?php foreach ($details as $d): ?>
                        <div class="col-md-4 mb-3">
                            <div class="form-check border p-2 rounded">
                                <input class="form-check-input lost-checkbox"
                                       type="checkbox"
                                       name="lost_books[]"
                                       value="<?= $d['book_item_id'] ?>">

                                <label class="form-check-label">
                                    <strong><?= esc($d['judul']) ?></strong><br>
                                    <small class="text-muted">Kode Inv: <?= esc($d['kode_inventaris'] ?: '-') ?></small>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>

                <hr>

                <div class="alert alert-info">
                    Denda dihitung otomatis berdasarkan keterlambatan.<br>
                    Buku hilang tidak dikenakan denda.
                </div>

                <button type="submit"
                        class="btn btn-primary">
                    Proses Pengembalian
                </button>

            </form>

        </div>
    </div>

</div>

<?= $this->endSection() ?>