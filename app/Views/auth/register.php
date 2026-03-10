<?= $this->extend('layout/auth') ?>
<?= $this->section('content') ?>

<div class="card shadow auth-card">
    <div class="card-body p-4">

        <h4 class="text-center mb-4">Register Member</h4>

        <?php if(session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <?php foreach(session()->getFlashdata('errors') as $error): ?>
                    <div><?= $error ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/auth/register" class="needs-validation" novalidate>
            <?= csrf_field() ?>

            <!-- Jenis Pengguna -->
            <div class="mb-3">
                <label>Jenis Pengguna</label>
                <select name="jenis_pengguna"
                        id="jenis_pengguna"
                        class="form-select"
                        required>
                    <option value="">-- Pilih --</option>
                    <option value="siswa" <?= old('jenis_pengguna')=='siswa'?'selected':'' ?>>
                        Siswa
                    </option>
                    <option value="guru" <?= old('jenis_pengguna')=='guru'?'selected':'' ?>>
                        Guru
                    </option>
                    <option value="pegawai" <?= old('jenis_pengguna')=='pegawai'?'selected':'' ?>>
                        Pegawai
                    </option>
                </select>
            </div>

             <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required value="<?= old('name') ?>">
            </div>

            <!-- Nomor Induk -->
            <div class="mb-3">
                <label id="label_member_code">Nomor Induk</label>
                <input type="text"
                    name="member_code"
                    id="member_code"
                    class="form-control"
                    value="<?= old('member_code') ?>"
                    required>
                <small class="text-muted">
                    Siswa → NIS | Guru/Pegawai → NIP/NUPTK
                </small>
            </div>

            <!-- NISN -->
            <div class="mb-3">
                <label id="label_member_code_nasional">Nomor Induk Nasional</label>
                <input type="text"
                    name="member_code_nation"
                    id="member_code_nation"
                    class="form-control"
                    value="<?= old('member_code_nation') ?>"
                    required>
                <small class="text-muted">
                    Siswa → NISN | Guru/Pegawai → NUPTK | Kosongkan Jika belum ada
                </small>
            </div>

            <!-- Dropdown Kelas -->
            <div class="mb-3">
                <label>Kelas</label>
                <select name="class"
                        id="class"
                        class="form-select"
                        required>
                    <option value="">-- Pilih --</option>
                    <?php foreach ($list_kelas as $kelas): ?>
                        <option value="<?= $kelas['id']; ?>">
                            <?= $kelas['class_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required value="<?= old('username') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password"
                        name="password"
                        id="registerPassword"
                        class="form-control"
                        required
                        minlength="6"
                        pattern="^(?=.*[A-Z])(?=.*[0-9]).{6,}$"
                        onkeyup="checkStrength(this.value); checkMatch();">
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="togglePassword('registerPassword')">
                        Show
                    </button>
                    <div class="invalid-feedback">
                        Password minimal 6 karakter, harus mengandung huruf besar dan angka.
                    </div>
                </div>

                <div class="progress mt-2" style="height: 8px;">
                    <div id="strengthBar" class="progress-bar" style="width: 0%;"></div>
                </div>
                <small id="strengthText" class="text-muted"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <div class="input-group">
                    <input type="password"
                        name="confirm_password"
                        id="confirmPassword"
                        class="form-control"
                        required
                        minlength="8"
                        onkeyup="checkMatch()">
                    <button type="button"
                            class="btn btn-outline-secondary"
                            onclick="togglePassword('confirmPassword')">
                        Show
                    </button>
                    <div class="invalid-feedback">
                        Konfirmasi password wajib diisi.
                    </div>
                </div>

                <small id="matchText" class="fw-semibold"></small>
            </div>

            <button type="submit" class="btn btn-success w-100">Register</button>

        </form>

        <hr>

        <div class="text-center mt-3">
            Sudah punya akun?
            <a href="/auth/login">Login</a>
        </div>

    </div>
</div>

<script>
document.getElementById('jenis_pengguna')
    .addEventListener('change', function(){

    const label = document.getElementById('label_member_code');

    if(this.value === 'siswa'){
        label.innerText = 'NIS';
    } else {
        label.innerText = 'NIP / Nomor Induk';
    }
});

<?= $this->endSection() ?>