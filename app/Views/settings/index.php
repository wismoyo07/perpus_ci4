<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-gear-fill text-primary"></i> Pengaturan Identitas Sekolah</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i> Perhatian!</h5>
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title mb-0 text-secondary fw-semibold">Detail Profil Lembaga</h5>
            </div>
            <div class="card-body">
                <form action="/admin/settings/update" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row">
                        <!-- Nama & Kode Lembaga -->
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-medium">Nama Sekolah / Lembaga <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lembaga" class="form-control" placeholder="Masukkan nama sekolah" value="<?= old('nama_lembaga', $setting['nama_lembaga']) ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Kode Lembaga</label>
                            <input type="text" name="kode_lembaga" class="form-control" placeholder="NPSN / Kode" value="<?= old('kode_lembaga', $setting['kode_lembaga']) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <!-- Kode Barang Buku (awal kode buku) -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Kode Barang Buku *</label>
                            <input type="text" name="kode_barang_buku" class="form-control" placeholder="03.11.01.01.001" pattern="^[0-9][0-9.]*$" required value="<?= old('kode_barang_buku', $setting['kode_barang_buku'] ?? '03.11.01.01.001') ?>">
                        </div>
                        <div class="col-md-8 mb-3 d-flex align-items-end">
                            <small class="text-muted">Kode barang aset untuk buku. Menjadi awal kode buku baru, misalnya <b><?= esc($setting['kode_barang_buku'] ?? '03.11.01.01.001') ?> / BOS / <?= date('Y') ?> / 001</b>. Kode buku yang sudah ada tidak berubah.</small>
                        </div>
                    </div>


                    <div class="row">
                        <!-- Status Akreditasi & Website -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Status Akreditasi</label>
                            <input type="text" name="status_akreditasi" class="form-control" placeholder="Contoh: A, B" value="<?= old('status_akreditasi', $setting['status_akreditasi']) ?>">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-medium">Website / Kontak (URL/Telp)</label>
                            <input type="text" name="website_url" class="form-control" placeholder="Contoh: https://sekolah.sch.id atau 021-xxxx" value="<?= old('website_url', $setting['website_url']) ?>">
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap sekolah"><?= old('alamat', $setting['alamat']) ?></textarea>
                    </div>

                    <hr class="my-4 text-muted">

                    <div class="row">
                        <!-- Kepala Sekolah & Wakil -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Nama Kepala Sekolah</label>
                            <input type="text" name="kepala_sekolah" class="form-control" placeholder="Nama Lengkap & Gelar" value="<?= old('kepala_sekolah', $setting['kepala_sekolah']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Nama Wakil Kepala Sekolah</label>
                            <input type="text" name="wakil_kepala_sekolah" class="form-control" placeholder="Nama Lengkap & Gelar" value="<?= old('wakil_kepala_sekolah', $setting['wakil_kepala_sekolah']) ?>">
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <!-- Logo Upload -->
                    <div class="row align-items-center mb-4">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="p-2 border rounded bg-light d-inline-block">
                                <img id="logo-preview" src="<?= $setting['logo'] ? base_url('uploads/settings/' . $setting['logo']) : 'https://placehold.co/120x120?text=No+Logo' ?>" alt="Logo Sekolah" class="img-fluid rounded" style="max-height: 120px; object-fit: contain;">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-medium">Logo Sekolah</label>
                            <input type="file" name="logo" id="logo-input" class="form-control" accept="image/png, image/jpeg, image/jpg">
                            <div class="form-text">Format yang didukung: PNG, JPG, JPEG. Ukuran maksimal: 2MB.</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="/admin/dashboard" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Preview logo secara instan sebelum diupload
    document.getElementById('logo-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logo-preview').setAttribute('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });
</script>

<?= $this->endSection() ?>
