<?= $this->extend('layout/auth') ?>
<?= $this->section('content') ?>

<!-- Register -->
<div class="nk-auth-card toggled" id="l-register">
    <div class="nk-auth-card-header">
        <h2>Registrasi Akun Baru</h2>
        <p>Isi semua form dengan data Anda</p>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <div><?= $error ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('auth/register') ?>" class="nk-auth-form" id="registerForm" novalidate>

        <div class="mb-3 mb-3">
            <label for="jenis_pengguna" class="form-label">Jenis Pengguna</label>
            <select name="jenis_pengguna" id="jenis_pengguna" class="form-select" required>
                <option value="">-- Pilih Jenis Pengguna --</option>
                <option value="siswa" <?= old('jenis_pengguna') == 'siswa' ? 'selected' : '' ?>>
                    Siswa
                </option>
                <option value="guru" <?= old('jenis_pengguna') == 'guru' ? 'selected' : '' ?>>
                    Guru
                </option>
                <option value="pegawai" <?= old('jenis_pengguna') == 'pegawai' ? 'selected' : '' ?>>
                    Pegawai
                </option>
            </select>
        </div>

        <!-- Pilihan Kelas (Hanya untuk Siswa) -->
        <div class="mb-3" id="class-container" style="display: none;">
            <label for="class" class="form-label">Kelas</label>
            <select name="class" id="class" class="form-select">
                <option value="">-- Pilih Kelas --</option>
                <?php if (!empty($list_kelas)): ?>
                    <?php foreach ($list_kelas as $k): ?>
                        <option value="<?= esc($k['class_name']) ?>" <?= old('class') == $k['class_name'] ? 'selected' : '' ?>>
                            <?= esc($k['class_name']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                <input type="text" name="name" class="form-control" placeholder="Isikan Nama Lengkap" required
                    value="<?= old('name') ?>">
            </div>
        </div>

        <!-- Nomor Induk -->
        <div class="mb-3">
            <label id="label_member_code" class="form-label">Nomor Induk</label>
            <input type="text" name="member_code" id="member_code" class="form-control"
                value="<?= old('member_code') ?>" placeholder="Siswa → NIS | Guru/Pegawai → NIP/Kode Pegawai" required>
        </div>

        <!-- NISN -->
        <div class="mb-3">
            <label id="label_member_code_nasional" class="form-label">Nomor Induk Nasional</label>
            <input type="text" name="member_code_nation" id="member_code_nation" class="form-control"
                value="<?= old('member_code_nation') ?>"
                placeholder="Siswa → NISN | Guru/Pegawai → NUPTK | Kosongkan Jika belum ada" required>
        </div>

        <!-- Nomor Telepon/HP -->
        <div class="mb-3">
            <label for="phone" class="form-label">Nomor Telepon / HP</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                <input type="text" name="phone" id="phone" class="form-control" placeholder="Masukkan nomor telepon/HP" required value="<?= old('phone') ?>">
            </div>
            <div class="invalid-feedback">Isikan nomor telepon yang valid.</div>
        </div>

        <!-- Sync Whatsapp Checkbox -->
        <div class="form-check mb-3 text-start">
            <input class="form-check-input" type="checkbox" id="syncWhatsapp" checked>
            <label class="form-check-label" for="syncWhatsapp">Nomor WhatsApp sama dengan nomor telepon/HP</label>
        </div>

        <!-- Nomor WhatsApp -->
        <div class="mb-3" id="whatsapp-container" style="display: none;">
            <label for="whatsapp" class="form-label">Nomor WhatsApp</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-brands fa-whatsapp text-success"></i></span>
                <input type="text" name="whatsapp" id="whatsapp" class="form-control" placeholder="Masukkan nomor WhatsApp berbeda" value="<?= old('whatsapp') ?>">
            </div>
            <div class="invalid-feedback">Isikan nomor WhatsApp yang valid.</div>
        </div>

        <div class="mb-3">
            <label for="regUsername" class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                <input type="text" class="form-control" id="regUsername" name="username" placeholder="Choose a username"
                    required autocomplete="username">
            </div>
            <div class="invalid-feedback">Please choose a username.</div>
        </div>

        <div class="mb-3">
            <label for="regEmail" class="form-label">Alamat Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                <input type="email" class="form-control" id="regEmail" name="email" placeholder="Enter your email"
                    required autocomplete="email">
            </div>
            <div class="invalid-feedback">Please enter a valid email address.</div>
        </div>

        <div class="mb-3">
            <label for="regPassword" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control" id="regPassword" name="password"
                    placeholder="Create a password" required minlength="8" autocomplete="new-password">
                <button type="button" class="input-group-text nk-toggle-password"
                    aria-label="Toggle password visibility" data-target="regPassword">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            <div class="invalid-feedback">Password must be at least 8 characters.</div>
        </div>

        <div class="mb-3">
            <label for="confirmPassword" class="form-label">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control" id="confirmPassword" name="confirm_password"
                    placeholder="Confirm your password" required autocomplete="new-password">
                <button type="button" class="input-group-text nk-toggle-password"
                    aria-label="Toggle password visibility" data-target="confirmPassword">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
        </div>

        <!-- <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" name="terms" required>
                        <label class="form-check-label" for="agreeTerms">
                            Saya telah membaca dan menyetujui <a href="#" class="nk-auth-link">Ketentuan Layanan</a> dan <a href="#" class="nk-auth-link">Kebijakan Privasi</a>
                        </label>
                        <div class="invalid-feedback">You must agree to the terms.</div>
                    </div> -->

        <button type="submit" class="btn btn-primary w-100 nk-auth-btn">
            <i class="fa-solid fa-user-plus me-2"></i>Buat Akun
        </button>
    </form>

    <!-- <div class="nk-auth-divider">
                    <span>or continue with</span>
                </div>

                <div class="nk-auth-social">
                    <button type="button" class="btn btn-outline-secondary">
                        <i class="fa-brands fa-google"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary">
                        <i class="fa-brands fa-github"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary">
                        <i class="fa-brands fa-microsoft"></i>
                    </button>
                </div> -->
    <div class="nk-auth-footer">
        <p>Sudah Punya Akun? <a href="<?= site_url('auth/login') ?>" data-auth-switch="#l-login">Masuk Disini</a></p>
    </div>
</div>

<script>
    // Toggle tampilan Kelas untuk Siswa secara dinamis
    document.getElementById('jenis_pengguna').addEventListener('change', function () {
        const classContainer = document.getElementById('class-container');
        if (this.value === 'siswa') {
            classContainer.style.display = 'block';
        } else {
            classContainer.style.display = 'none';
            document.getElementById('class').value = '';
        }
    });

    // Inisialisasi saat load halaman (untuk validation fallback)
    window.addEventListener('DOMContentLoaded', () => {
        const jenisVal = document.getElementById('jenis_pengguna').value;
        const classContainer = document.getElementById('class-container');
        if (jenisVal === 'siswa') {
            classContainer.style.display = 'block';
        }
    });

    // Toggle Whatsapp input based on checkbox
    const phoneInput = document.getElementById('phone');
    const syncCheckbox = document.getElementById('syncWhatsapp');
    const whatsappContainer = document.getElementById('whatsapp-container');
    const whatsappInput = document.getElementById('whatsapp');

    function updateWhatsapp() {
        if (syncCheckbox.checked) {
            whatsappContainer.style.display = 'none';
            whatsappInput.value = phoneInput.value;
            whatsappInput.removeAttribute('required');
        } else {
            whatsappContainer.style.display = 'block';
            whatsappInput.setAttribute('required', 'required');
            if (!whatsappInput.value) {
                whatsappInput.value = phoneInput.value;
            }
        }
    }

    if (syncCheckbox && phoneInput) {
        syncCheckbox.addEventListener('change', updateWhatsapp);
        phoneInput.addEventListener('input', function() {
            if (syncCheckbox.checked) {
                whatsappInput.value = this.value;
            }
        });
        
        // Initial call
        updateWhatsapp();
    }
</script>

<?= $this->endSection() ?>