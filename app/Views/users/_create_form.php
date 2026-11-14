<?php
// Nilai isian: input lama setelah validasi gagal, selalu di-escape sekali.
$val   = static fn (string $key, string $default = '') => esc((string) (old($key, null, false) ?? $default));
$jenis = (string) (old('jenis_pengguna', null, false) ?? 'siswa');
$kelas = (string) (old('class', null, false) ?? '');
?>
<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i>Periksa kembali isian berikut:
        <ul class="mb-0 mt-1">
            <?php foreach ((array) session()->getFlashdata('errors') as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="/users/store">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="name" class="form-label small fw-bold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control" maxlength="100" required value="<?= $val('name') ?>">
    </div>

    <div class="mb-3">
        <label for="username" class="form-label small fw-bold text-secondary">Username <span class="text-danger">*</span></label>
        <input type="text" name="username" id="username" class="form-control" minlength="4" maxlength="50" required value="<?= $val('username') ?>">
        <small class="text-muted">Untuk login. Huruf, angka, titik, garis bawah, atau tanda minus, tanpa spasi.</small>
    </div>

    <div class="mb-3">
        <label for="jenis_pengguna" class="form-label small fw-bold text-secondary">Jenis Pengguna <span class="text-danger">*</span></label>
        <select name="jenis_pengguna" id="jenis_pengguna" class="form-select" required>
            <?php foreach (['siswa' => 'Siswa', 'guru' => 'Guru', 'pegawai' => 'Pegawai'] as $v => $label): ?>
                <option value="<?= $v ?>" <?= $jenis === $v ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="member_code" class="form-label small fw-bold text-secondary"><span class="lbl-siswa">NIS</span><span class="lbl-pegawai">NIP / Kode Pegawai</span> <span class="text-danger">*</span></label>
            <input type="text" name="member_code" id="member_code" class="form-control" maxlength="50" required value="<?= $val('member_code') ?>">
        </div>
        <div class="col-md-6">
            <label for="member_code_nation" class="form-label small fw-bold text-secondary"><span class="lbl-siswa">NISN</span><span class="lbl-pegawai">NUPTK</span> <span class="text-muted fw-normal">(opsional)</span></label>
            <input type="text" name="member_code_nation" id="member_code_nation" class="form-control" maxlength="50" value="<?= $val('member_code_nation') ?>">
        </div>
    </div>

    <div class="mb-3" id="kelasGroup">
        <label for="class" class="form-label small fw-bold text-secondary">Kelas</label>
        <select name="class" id="class" class="form-select">
            <option value="">- Pilih Kelas -</option>
            <?php foreach ($list_kelas ?? [] as $k): ?>
                <option value="<?= esc($k['class_name']) ?>" <?= $k['class_name'] === $kelas ? 'selected' : '' ?>><?= esc($k['class_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="phone" class="form-label small fw-bold text-secondary">Nomor Telepon</label>
            <input type="text" name="phone" id="phone" class="form-control" maxlength="20" value="<?= $val('phone') ?>">
        </div>
        <div class="col-md-6">
            <label for="whatsapp" class="form-label small fw-bold text-secondary">Nomor WhatsApp</label>
            <input type="text" name="whatsapp" id="whatsapp" class="form-control" maxlength="20" value="<?= $val('whatsapp') ?>">
        </div>
    </div>

    <div class="alert alert-info small py-2">
        <i class="fa-solid fa-circle-info me-1"></i>
        Akun dibuat sebagai <b>Member</b> dan langsung tervalidasi. Password awal <code>123456</code>, wajib diganti saat login pertama.
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan</button>
        <a href="/users" class="btn btn-outline-secondary px-4 fw-semibold">Batal</a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const jenis = document.getElementById('jenis_pengguna');
    function sync() {
        const siswa = jenis.value === 'siswa';
        document.getElementById('kelasGroup').classList.toggle('d-none', !siswa);
        document.querySelectorAll('.lbl-siswa').forEach(e => e.classList.toggle('d-none', !siswa));
        document.querySelectorAll('.lbl-pegawai').forEach(e => e.classList.toggle('d-none', siswa));
    }
    jenis.addEventListener('change', sync);
    sync();
});
</script>
