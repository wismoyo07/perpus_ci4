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
                  action="<?= isset($book) ? site_url('books/update/'.$book['id']) : site_url('books/store') ?>">

                <?= csrf_field() ?>

                <div class="row">

                    <!-- KODE BUKU -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kode Buku</label>
                        <input type="text"
                               name="kode_buku"
                               class="form-control"
                               placeholder="<?= esc($kodeBarang) ?> / BOS / <?= date('Y') ?> / 005"
                               pattern="^[0-9][0-9.]*\s*\/\s*[A-Za-z0-9-]+\s*\/\s*\d{4}\s*\/\s*\d{3,}$"
                               value="<?= old('kode_buku', $book['kode_buku'] ?? '') ?>">
                        <small class="text-muted">
                            <?php if (isset($book)): ?>
                                Format: kode barang / sumber dana / tahun / nomor urut.
                            <?php else: ?>
                                Kosongkan agar dibuat otomatis dari kode barang <b><?= esc($kodeBarang) ?></b>, sumber dana, dan tahun pengadaan di bawah.
                            <?php endif; ?>
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

                <h6 class="text-secondary mb-3">Klasifikasi &amp; Pengadaan</h6>

                <div class="row">

                    <!-- KLASIFIKASI DDC (kelas / divisi / seksi) -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Klasifikasi DDC</label>
                        <?php $selectedCategory = old('category_id', $book['category_id'] ?? ''); ?>
                        <select name="category_id" class="form-select">
                            <option value="">— Belum diklasifikasi —</option>
                            <?php foreach ($categoryGroups as $group): ?>
                                <optgroup label="<?= esc($group['label']) ?>">
                                    <?php foreach ($group['items'] as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= (string) $selectedCategory === (string) $c['id'] ? 'selected' : '' ?>>
                                            <?= $c['level'] === 'seksi' ? '&nbsp;&nbsp;&nbsp;' : '' ?><?= esc($c['code_ddc']) ?> — <?= esc($c['desc_ddc']) ?><?= $c['level'] === 'kelas' ? ' (umum)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Pilih divisi yang paling sesuai. Jika Nomor DDC diisi, klasifikasi dipilih otomatis (seksi → divisi → kelas).</small>
                    </div>

                    <!-- NOMOR DDC -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nomor DDC <span class="text-muted">(opsional)</span></label>
                        <input type="text"
                               name="ddc_number"
                               class="form-control"
                               placeholder="005.133"
                               pattern="^\d{3}(\.\d+)?$"
                               value="<?= old('ddc_number', $book['ddc_number'] ?? '') ?>">
                        <small class="text-muted">Jika diisi, klasifikasi dipilih otomatis dari nomor ini. Dipakai untuk nomor panggil di label.</small>
                    </div>

                    <?php if (!isset($book)): ?>
                        <!-- SUMBER DANA -->
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Sumber Dana *</label>
                            <?php $selectedSource = old('sumber_dana', 'BOS'); ?>
                            <select name="sumber_dana" class="form-select" required>
                                <?php foreach ($sources as $s): ?>
                                    <option value="<?= esc($s['kode']) ?>" <?= $selectedSource === $s['kode'] ? 'selected' : '' ?>>
                                        <?= esc($s['kode']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- TAHUN PENGADAAN -->
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Tahun *</label>
                            <input type="number"
                                   name="tahun_pengadaan"
                                   class="form-control"
                                   min="1900"
                                   max="<?= date('Y') + 1 ?>"
                                   required
                                   value="<?= old('tahun_pengadaan', date('Y')) ?>">
                        </div>
                    <?php endif; ?>

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
                               <?= isset($book) ? 'disabled' : '' ?>
                               value="<?= old('stok_total', $book['stok_total'] ?? 0) ?>">
                        <?php if(isset($book)): ?>
                            <small class="text-muted">Kelola stok via <a href="/books/items/<?= $book['id'] ?>">Inventaris Salinan</a></small>
                        <?php endif; ?>
                    </div>

                    <!-- STOK TERSEDIA -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Stok Tersedia *</label>
                        <input type="number"
                               name="stok_tersedia"
                               min="0"
                               required
                               class="form-control"
                               <?= isset($book) ? 'disabled' : '' ?>
                               value="<?= old('stok_tersedia', $book['stok_tersedia'] ?? 0) ?>">
                        <?php if(!isset($book)): ?>
                            <small class="text-muted">
                                Tidak boleh melebihi stok total
                            </small>
                        <?php endif; ?>
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