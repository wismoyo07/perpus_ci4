<?= $this->extend('layout/auth') ?>
<?= $this->section('content') ?>

    <!-- Register -->
            <div class="nk-auth-card toggled" id="l-register">
                <div class="nk-auth-card-header">
                    <h2>Registrasi Akun Baru</h2>
                    <p>Isi semua form dengan data Anda</p>
                </div>

            <?php if(session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach(session()->getFlashdata('errors') as $error): ?>
                        <div><?= $error ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

                <form method="post" action="<?= site_url('auth/register') ?>" class="nk-auth-form" id="registerForm" novalidate>

                    <div class="mb-3 mb-3">
                        <label for="regJenis" class="form-label">Jenis Pengguna</label>
                        <select name="jenis_pengguna"
                                id="jenis_pengguna"
                                class="form-select"
                                required>
                            <option value="">-- Pilih Jenis Pengguna --</option>
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
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                            <input type="text" name="name" class="form-control" placeholder="Isikan Nama Lengkap" required value="<?= old('name') ?>">
                        </div>
                    </div>

                    <!-- Nomor Induk -->
                    <div class="mb-3">
                        <label id="label_member_code" class="form-label">Nomor Induk</label>
                        <input type="text"
                            name="member_code"
                            id="member_code"
                            class="form-control"
                            value="<?= old('member_code') ?>"
                            placeholder="Siswa → NIS | Guru/Pegawai → NIP/Kode Pegawai"
                            required>
                    </div>

                    <!-- NISN -->
                    <div class="mb-3">
                        <label id="label_member_code_nasional" class="form-label">Nomor Induk Nasional</label>
                        <input type="text"
                            name="member_code_nation"
                            id="member_code_nation"
                            class="form-control"
                            value="<?= old('member_code_nation') ?>"
                            placeholder="Siswa → NISN | Guru/Pegawai → NUPTK | Kosongkan Jika belum ada"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="regUsername" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                            <input type="text" class="form-control" id="regUsername" name="username" placeholder="Choose a username" required autocomplete="username">
                        </div>
                        <div class="invalid-feedback">Please choose a username.</div>
                    </div>

                    <div class="mb-3">
                        <label for="regEmail" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" class="form-control" id="regEmail" name="email" placeholder="Enter your email" required autocomplete="email">
                        </div>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>

                    <div class="mb-3">
                        <label for="regPassword" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" class="form-control" id="regPassword" name="password" placeholder="Create a password" required minlength="8" autocomplete="new-password">
                            <button type="button" class="input-group-text nk-toggle-password" aria-label="Toggle password visibility" data-target="regPassword">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Password must be at least 8 characters.</div>
                    </div>

                    <!-- <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" name="terms" required>
                        <label class="form-check-label" for="agreeTerms">
                            Saya telah membaca dan menyetujui <a href="#" class="nk-auth-link">Ketentuan Layanan</a> dan <a href="#" class="nk-auth-link">Kebijakan Privasi</a>
                        </label>
                        <div class="invalid-feedback">You must agree to the terms.</div>
                    </div> -->

                    <button type="submit" class="btn btn-primary w-100 nk-auth-btn">
                        <i class="fa-solid fa-user-plus me-2"></i>Create Account
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
                    <p>Already have an account? <a href="<?= site_url('auth/login') ?>" data-auth-switch="#l-login">Sign in</a></p>
                </div>
            </div>

<?= $this->endSection() ?>