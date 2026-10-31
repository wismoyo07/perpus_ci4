<?php
// Nilai isian: input lama (setelah validasi gagal) atau data tersimpan, selalu di-escape.
$val = static fn (string $key, $default = '') => esc((string) (old($key, null, false) ?? ($setting[$key] ?? $default)));
?>
<form action="/admin/settings/update" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row">
        <!-- Nama & Kode Lembaga -->
        <div class="col-md-8 mb-3">
            <label class="form-label fw-medium">Nama Sekolah / Lembaga <span class="text-danger">*</span></label>
            <input type="text" name="nama_lembaga" class="form-control" placeholder="Masukkan nama sekolah" maxlength="200" value="<?= $val('nama_lembaga') ?>" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label fw-medium">Kode Lembaga</label>
            <input type="text" name="kode_lembaga" class="form-control" placeholder="NPSN / Kode" maxlength="50" value="<?= $val('kode_lembaga') ?>">
        </div>
    </div>

    <div class="row">
        <!-- Kode Barang Buku & Batas Hari Denda -->
        <div class="col-md-6 mb-3">
            <label class="form-label fw-medium">Kode Barang Buku <span class="text-danger">*</span></label>
            <input type="text" name="kode_barang_buku" class="form-control" placeholder="03.11.01.01.001" pattern="^[0-9][0-9.]*$" maxlength="30" required value="<?= $val('kode_barang_buku', '03.11.01.01.001') ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-medium">Batas Maksimal Hari Denda <span class="text-danger">*</span></label>
            <input type="number" name="maks_hari_denda" class="form-control" min="0" max="365" required value="<?= $val('maks_hari_denda', 10) ?>">
            <small class="text-muted">Denda keterlambatan per buku dihitung paling banyak sekian hari. Isi 0 untuk tanpa batas.</small>
        </div>
    </div>

    <div class="row">
        <!-- Status Akreditasi & Website -->
        <div class="col-md-4 mb-3">
            <label class="form-label fw-medium">Status Akreditasi</label>
            <input type="text" name="status_akreditasi" class="form-control" placeholder="Contoh: Unggul, Baik Sekali, A" maxlength="50" value="<?= $val('status_akreditasi') ?>">
        </div>
        <div class="col-md-8 mb-3">
            <label class="form-label fw-medium">Website / Kontak (URL/Telp)</label>
            <input type="text" name="website_url" class="form-control" placeholder="Contoh: https://sekolah.sch.id atau 021-xxxx" maxlength="255" value="<?= $val('website_url') ?>">
        </div>
    </div>

    <!-- Alamat -->
    <div class="mb-3">
        <label class="form-label fw-medium">Alamat Lengkap</label>
        <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap sekolah"><?= $val('alamat') ?></textarea>
    </div>

    <hr class="my-4 text-muted">

    <div class="row">
        <!-- Kepala Sekolah & Wakil -->
        <div class="col-md-6 mb-3">
            <label class="form-label fw-medium">Nama Kepala Sekolah</label>
            <input type="text" name="kepala_sekolah" class="form-control" placeholder="Nama Lengkap & Gelar" maxlength="150" value="<?= $val('kepala_sekolah') ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-medium">Nama Wakil Kepala Sekolah</label>
            <input type="text" name="wakil_kepala_sekolah" class="form-control" placeholder="Nama Lengkap & Gelar" maxlength="150" value="<?= $val('wakil_kepala_sekolah') ?>">
        </div>
    </div>

    <hr class="my-4 text-muted">

    <!-- Logo Upload -->
    <div class="row align-items-center mb-4">
        <div class="col-md-3 text-center mb-3 mb-md-0">
            <div class="p-2 border rounded bg-light d-inline-block">
                <img id="logo-preview" src="<?= !empty($setting['logo']) ? esc(base_url('uploads/settings/' . $setting['logo']), 'attr') : 'https://placehold.co/120x120?text=No+Logo' ?>" alt="Logo Sekolah" class="img-fluid rounded" style="max-height: 120px; object-fit: contain;">
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
