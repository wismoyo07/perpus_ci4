<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h4 class="text-dark fw-bold"><i class="fa-solid fa-file-excel text-success me-2"></i> Impor Anggota dari Excel</h4>
            <p class="text-muted mb-0">Tambahkan banyak siswa, guru, atau pegawai sekaligus dari file Excel.</p>
        </div>
        <a href="/users" class="btn btn-outline-secondary shadow-sm"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-1"></i> <?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 fw-bold"><span class="badge bg-success me-2">1</span> Unduh & isi template</h5>
                </div>
                <div class="card-body">
                    <a href="/users/import/template" class="btn btn-success fw-bold mb-3">
                        <i class="fa-solid fa-download me-1"></i> Unduh Template Excel
                    </a>
                    <p class="small mb-2">Kolom di template:</p>
                    <ul class="small mb-3">
                        <?php foreach ($columns as [$label, $required]): ?>
                            <li><?= esc($label) ?><?= $required ? ' <span class="text-danger">*</span>' : ' <span class="text-muted">(opsional)</span>' ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <ul class="small text-muted mb-0">
                        <li>Jenis pengguna: <code>siswa</code>, <code>guru</code>, atau <code>pegawai</code>.</li>
                        <li>Kelas hanya untuk siswa dan harus sesuai data kelas<?= $classes ? ', misalnya <code>' . esc($classes[0]) . '</code>' : '' ?>.</li>
                        <li>Username boleh kosong; dibuat otomatis dari NIS / NIP.</li>
                        <li>Maksimal <?= $maxRows ?> anggota per file, ukuran file maksimal 2 MB.</li>
                        <li>Password awal akun baru <code>123456</code>, wajib diganti saat login pertama.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 fw-bold"><span class="badge bg-success me-2">2</span> Unggah file</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="/users/import/preview" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="file">File Excel (.xlsx)</label>
                            <input type="file" name="file" id="file" class="form-control" required
                                   accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jika NIS / NIP sudah terdaftar</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode" id="modeSkip" value="lewati" checked>
                                <label class="form-check-label" for="modeSkip"><b>Lewati</b> — data lama tidak diubah</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode" id="modeUpdate" value="perbarui">
                                <label class="form-check-label" for="modeUpdate">
                                    <b>Perbarui</b> nama, NISN/NUPTK, kelas, dan kontak
                                    <div class="small text-muted">Cocok untuk kenaikan kelas di awal tahun ajaran. Isian kosong tidak menghapus data lama.</div>
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Periksa Data
                        </button>
                        <div class="small text-muted mt-2">Data belum disimpan pada langkah ini. Anda akan melihat pratinjau terlebih dahulu.</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
