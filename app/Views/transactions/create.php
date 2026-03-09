<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Transaksi Peminjaman</h4>
        <a href="/transactions" class="btn btn-outline-secondary btn-sm">
            ← Kembali
        </a>
    </div>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="post" action="/transactions/store">

                <?= csrf_field() ?>

                <!-- PILIH MEMBER -->
                <div class="mb-3">
                    <label class="form-label">Pilih Member *</label>
                    <select name="member_id" class="form-select" required>
                        <option value="">-- Pilih Member --</option>
                        <?php foreach ($members as $m): ?>
                            <option value="<?= $m['id'] ?>">
                                <?= esc($m['name']) ?> 
                                (<?= esc($m['jenis_pengguna']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <hr>

                <!-- PILIH BUKU -->
                <div class="mb-3">
                    <label class="form-label">
                        Pilih Buku (maksimal sesuai jenis anggota)
                    </label>

                    <div class="row" style="max-height:300px; overflow-y:auto;">
                        <?php foreach ($books as $b): ?>
                            <div class="col-md-4 mb-2">
                                <div class="form-check border p-2 rounded">
                                    <input class="form-check-input book-checkbox"
                                           type="checkbox"
                                           name="book_ids[]"
                                           value="<?= $b['id'] ?>"
                                           data-stock="<?= $b['stok_tersedia'] ?>">

                                    <label class="form-check-label">
                                        <strong><?= esc($b['judul']) ?></strong><br>
                                        <small>Stok: <?= $b['stok_tersedia'] ?></small>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <hr>

                <!-- INFO OTOMATIS -->
                <div class="alert alert-info">
                    Lama peminjaman: <strong>10 hari</strong><br>
                    Jatuh tempo otomatis dihitung sistem.
                </div>

                <button type="submit" class="btn btn-primary">
                    Proses Peminjaman
                </button>

            </form>

        </div>
    </div>

</div>

<!-- LIMIT CHECK SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    const memberSelect = document.querySelector('[name="member_id"]');
    const checkboxes = document.querySelectorAll('.book-checkbox');

    function getLimit() {
        const selectedText = memberSelect.options[memberSelect.selectedIndex]?.text || '';

        if (selectedText.includes('guru') || selectedText.includes('pegawai')) {
            return 5;
        }
        return 3;
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {

            const checked = document.querySelectorAll('.book-checkbox:checked');
            const limit = getLimit();

            if (checked.length > limit) {
                alert('Maksimal ' + limit + ' buku');
                this.checked = false;
            }
        });
    });

});
</script>

<?= $this->endSection() ?>