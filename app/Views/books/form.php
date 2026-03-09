<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><?= isset($book) ? 'Edit Buku' : 'Tambah Buku' ?></h4>
        <a href="/books" class="btn btn-outline-secondary btn-sm">
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

            <form method="post"
                  action="<?= isset($book) ? '/books/update/'.$book['id'] : '/books/store' ?>">

                <?= csrf_field() ?>

                <div class="row">

                    <!-- KODE BUKU -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kode Buku</label>
                        <input type="text"
                               name="kode_buku"
                               class="form-control"
                               value="<?= old('kode_buku', $book['kode_buku'] ?? '') ?>">
                        <small class="text-muted">
                            Kosongkan jika ingin auto-generate (opsional)
                        </small>
                    </div>

                    <!-- JUDUL -->
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Judul Buku *</label>
                        <input type="text"
                               name="judul"
                               required
                               class="form-control"
                               value="<?= old('judul', $book['judul'] ?? '') ?>">
                    </div>

                    <!-- PENGARANG -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pengarang</label>
                        <input type="text"
                               name="pengarang"
                               class="form-control"
                               value="<?= old('pengarang', $book['pengarang'] ?? '') ?>">
                    </div>

                    <!-- PENERBIT -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Penerbit</label>
                        <input type="text"
                               name="penerbit"
                               class="form-control"
                               value="<?= old('penerbit', $book['penerbit'] ?? '') ?>">
                    </div>

                </div>

                <hr>

                <div class="row">

                    <!-- STOK TOTAL -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Stok Total *</label>
                        <input type="number"
                               name="stok_total"
                               min="0"
                               required
                               class="form-control"
                               value="<?= old('stok_total', $book['stok_total'] ?? 0) ?>">
                    </div>

                    <!-- STOK TERSEDIA -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Stok Tersedia *</label>
                        <input type="number"
                               name="stok_tersedia"
                               min="0"
                               required
                               class="form-control"
                               value="<?= old('stok_tersedia', $book['stok_tersedia'] ?? 0) ?>">
                        <small class="text-muted">
                            Tidak boleh melebihi stok total
                        </small>
                    </div>

                    <!-- STATUS (Edit Only) -->
                    <?php if(isset($book)): ?>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <input type="text"
                                   class="form-control"
                                   value="<?= $book['is_active'] ? 'Aktif' : 'Archived' ?>"
                                   disabled>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <?= isset($book) ? 'Update Buku' : 'Simpan Buku' ?>
                    </button>

                    <a href="/books" class="btn btn-light">
                        Batal
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const totalInput = document.querySelector('[name="stok_total"]');
        const tersediaInput = document.querySelector('[name="stok_tersedia"]');

        function validateStock() {
            let total = parseInt(totalInput.value) || 0;
            let tersedia = parseInt(tersediaInput.value) || 0;

            if (tersedia > total) {
                tersediaInput.classList.add('is-invalid');
            } else {
                tersediaInput.classList.remove('is-invalid');
            }
        }

        totalInput.addEventListener('input', validateStock);
        tersediaInput.addEventListener('input', validateStock);
    });
</script>

<?= $this->endSection() ?>