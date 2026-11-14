<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h4 class="text-dark fw-bold"><i class="fa-solid fa-user-pen text-primary me-2"></i> Edit Informasi Pengguna</h4>
            <p class="text-muted mb-0">Perbarui profil anggota, jenis pengguna, atau tingkat otoritas sistem.</p>
        </div>
        <a href="/users" class="btn btn-outline-secondary shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- ERROR MESSAGES -->
    <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i>Periksa kembali isian berikut:
        <ul class="mb-0 mt-1">
            <?php foreach ((array) session()->getFlashdata('errors') as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-3 overflow-hidden" style="max-width: 600px;">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-circle-user text-muted me-2"></i> Form Edit User</h5>
        </div>
        <div class="card-body p-4">
            <form method="post" action="/users/update/<?= $user['id'] ?>">
                <?= csrf_field() ?>

                <!-- USERNAME (READONLY & DISABLED) -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Username</label>
                    <input type="text" class="form-control bg-light" value="<?= esc($user['username']) ?>" readonly disabled>
                    <small class="text-muted">Username digunakan untuk login dan tidak dapat diubah.</small>
                </div>

                <!-- NAMA LENGKAP -->
                <div class="mb-3">
                    <label for="name" class="form-label small fw-bold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="<?= esc(old('name', null, false) ?? $user['name']) ?>" class="form-control" required>
                </div>

                <!-- DYNAMIC INDENTITAS (NIS/NISN ATAU NIP/NUPTK) -->
                <?php 
                $currentRole = session('role');
                $isReadOnly = ($currentRole !== 'admin') ? 'readonly bg-light' : '';
                
                // Judul label dinamis
                if ($user['jenis_pengguna'] === 'siswa') {
                    $labelCode1 = 'NIS (Nomor Induk Siswa) *';
                    $labelCode2 = 'NISN (Nomor Induk Siswa Nasional)';
                    $placeholder1 = 'Masukkan NIS siswa';
                    $placeholder2 = 'Masukkan NISN siswa (opsional)';
                } else {
                    $labelCode1 = 'NIP / Kode Pegawai *';
                    $labelCode2 = 'NUPTK';
                    $placeholder1 = 'Masukkan NIP atau Kode Pegawai';
                    $placeholder2 = 'Masukkan NUPTK (opsional)';
                }
                ?>

                <!-- Nomor Induk Utama (member_code) -->
                <div class="mb-3">
                    <label for="member_code" class="form-label small fw-bold text-secondary"><?= $labelCode1 ?></label>
                    <input type="text" name="member_code" id="member_code" 
                           value="<?= esc($user['member_code']) ?>" 
                           class="form-control" <?= $isReadOnly ?> required placeholder="<?= $placeholder1 ?>">
                    <?php if ($currentRole !== 'admin'): ?>
                        <small class="text-muted d-block mt-1"><i class="fa-solid fa-lock text-warning me-1"></i>Hanya Admin yang dapat mengubah Nomor Induk ini.</small>
                    <?php endif; ?>
                </div>

                <!-- Nomor Induk Nasional/Tambahan (member_code_nation) -->
                <div class="mb-3">
                    <label for="member_code_nation" class="form-label small fw-bold text-secondary"><?= $labelCode2 ?></label>
                    <input type="text" name="member_code_nation" id="member_code_nation" 
                           value="<?= esc($user['member_code_nation'] ?? '') ?>" 
                           class="form-control" <?= $isReadOnly ?> placeholder="<?= $placeholder2 ?>">
                    <?php if ($currentRole !== 'admin'): ?>
                        <small class="text-muted d-block mt-1"><i class="fa-solid fa-lock text-warning me-1"></i>Hanya Admin yang dapat mengubah data ini.</small>
                    <?php endif; ?>
                </div>

                <!-- KELAS (SISWA) -->
                <?php if ($user['jenis_pengguna'] === 'siswa'): ?>
                    <div class="mb-3">
                        <label for="class" class="form-label small fw-bold text-secondary">Kelas</label>
                        <select name="class" id="class" class="form-select">
                            <option value="">- Pilih Kelas -</option>
                            <?php $kelasAda = false; ?>
                            <?php foreach ($list_kelas as $k): ?>
                                <?php $kelasAda = $kelasAda || $k['class_name'] === $user['class']; ?>
                                <option value="<?= esc($k['class_name']) ?>" <?= $k['class_name'] === $user['class'] ? 'selected' : '' ?>><?= esc($k['class_name']) ?></option>
                            <?php endforeach; ?>
                            <?php if (!$kelasAda && !empty($user['class'])): ?>
                                <option value="<?= esc($user['class']) ?>" selected><?= esc($user['class']) ?> (tidak ada di data kelas)</option>
                            <?php endif; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- KONTAK -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="phone" class="form-label small fw-bold text-secondary">Nomor Telepon</label>
                        <input type="text" name="phone" id="phone" maxlength="20" class="form-control"
                               value="<?= esc(old('phone', null, false) ?? ($user['phone'] ?? '')) ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="whatsapp" class="form-label small fw-bold text-secondary">Nomor WhatsApp</label>
                        <input type="text" name="whatsapp" id="whatsapp" maxlength="20" class="form-control"
                               value="<?= esc(old('whatsapp', null, false) ?? ($user['whatsapp'] ?? '')) ?>">
                    </div>
                </div>

                <!-- JENIS PENGGUNA (GURU/PEGAWAI BISA DIUBAH, SISWA TETAP SISWA) -->
                <?php if($user['jenis_pengguna'] !== 'siswa'): ?>
                    <div class="mb-3">
                        <label for="jenis_pengguna" class="form-label small fw-bold text-secondary">Jenis Pengguna <span class="text-danger">*</span></label>
                        <select name="jenis_pengguna" id="jenis_pengguna" class="form-select">
                            <option value="guru" <?= $user['jenis_pengguna']=='guru'?'selected':'' ?>>Guru</option>
                            <option value="pegawai" <?= $user['jenis_pengguna']=='pegawai'?'selected':'' ?>>Pegawai</option>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="jenis_pengguna" value="siswa">
                <?php endif; ?>

                <!-- ROLE SISTEM (HANYA UNTUK ADMIN JIKA JENIS GURU/PEGAWAI) -->
                <?php if($currentRole === 'admin' && in_array($user['jenis_pengguna'], ['guru','pegawai'])): ?>
                    <div class="mb-4">
                        <label for="role" class="form-label small fw-bold text-secondary">Role Otoritas Sistem <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select">
                            <option value="member" <?= $user['role']=='member'?'selected':'' ?>>Member (Hanya Akses Katalog)</option>
                            <option value="staff" <?= $user['role']=='staff'?'selected':'' ?>>Staff (Petugas Sirkulasi)</option>
                            <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin (Akses Penuh)</option>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                    </button>
                    <a href="/users" class="btn btn-outline-secondary px-4 fw-semibold">Batal</a>
                </div>

            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>