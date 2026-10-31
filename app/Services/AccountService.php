<?php

namespace App\Services;

use App\Libraries\PasswordPolicy;
use App\Models\UserModel;

/**
 * Ganti password oleh pengguna sendiri (halaman Profil dan wajib ganti password).
 */
class AccountService
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper('password');
    }

    /**
     * @param string|null $current password lama; null jika tidak diminta (wajib ganti setelah login)
     *
     * @return string[] pesan kesalahan; kosong jika berhasil disimpan
     */
    public function changePassword(int $userId, ?string $current, string $new, string $confirm): array
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            return ['Akun tidak ditemukan.'];
        }

        if ($current !== null && !self::matches($current, $user['password'])) {
            return ['Password lama salah.'];
        }

        $errors = PasswordPolicy::errors($new, $user['username']);
        if ($new !== $confirm) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }
        if ($errors === [] && self::matches($new, $user['password'])) {
            $errors[] = 'Password baru tidak boleh sama dengan password lama.';
        }
        if ($errors !== []) {
            return $errors;
        }

        $this->userModel->update($userId, [
            'password'             => hashPassword($new),
            'must_change_password' => 0,
        ]);
        (new LoggingService())->log('Ganti password sendiri', $userId, $userId);

        return [];
    }

    /** Cocokkan password dengan hash (dengan pepper, atau hash lama tanpa pepper). */
    private static function matches(string $password, string $hash): bool
    {
        return verifyPassword($password, $hash) || password_verify($password, $hash);
    }
}
