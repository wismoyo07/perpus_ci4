<?= $this->extend('layout/auth2') ?>
<?= $this->section('content') ?>

    <!-- Register -->
            <div class="nk-auth-card toggled" id="l-register">
                <div class="nk-auth-card-header">
                    <h2>Registrasi Akun Baru</h2>
                    <p>Isi semua form dengan data Anda</p>
                </div>

                <form method="post" action="/auth/register" class="nk-auth-form" id="registerForm" novalidate>
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
                    <p>Already have an account? <a href="/auth/login" data-auth-switch="#l-login">Sign in</a></p>
                </div>
            </div>

<?= $this->endSection() ?>