<?php

namespace App\Libraries;

/**
 * Aturan password yang sama untuk registrasi, wajib ganti password, dan halaman Profil.
 *
 * - 8–64 karakter (bcrypt hanya membaca 72 byte pertama, termasuk pepper).
 * - Wajib ada huruf kecil, huruf besar, angka, dan karakter khusus.
 * - Hanya huruf/angka/simbol ASCII yang terlihat: tanpa spasi, huruf beraksen, atau emoji.
 *   (Spasi di awal/akhir juga terpotong saat login.)
 * - Tidak boleh sama dengan username atau password default reset (123456).
 */
class PasswordPolicy
{
    public const MIN_LENGTH = 8;
    public const MAX_LENGTH = 64;
    public const DEFAULT_RESET = '123456';
    public const SYMBOLS = '! @ # $ % ^ & * ( ) - _ = + [ ] { } ; : \' " , . < > / ? \\ | ` ~';

    /** Ringkasan aturan untuk ditampilkan di form. */
    public static function hints(): array
    {
        return [
            self::MIN_LENGTH . '–' . self::MAX_LENGTH . ' karakter',
            'Ada huruf kecil (a–z) dan huruf besar (A–Z)',
            'Ada angka (0–9)',
            'Ada karakter khusus: ' . self::SYMBOLS,
            'Tidak boleh memakai spasi, huruf beraksen (é, ñ), atau emoji',
            'Tidak boleh sama dengan username',
        ];
    }

    /**
     * @return string[] daftar pelanggaran; kosong jika password memenuhi aturan
     */
    public static function errors(string $password, ?string $username = null): array
    {
        $errors = [];
        $length = strlen($password);

        if ($length < self::MIN_LENGTH) {
            $errors[] = 'Password minimal ' . self::MIN_LENGTH . ' karakter.';
        }
        if ($length > self::MAX_LENGTH) {
            $errors[] = 'Password maksimal ' . self::MAX_LENGTH . ' karakter.';
        }
        if (preg_match('/[^\x21-\x7E]/', $password)) {
            $errors[] = 'Password tidak boleh memakai spasi, huruf beraksen, atau emoji.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password harus memuat huruf kecil.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password harus memuat huruf besar.';
        }
        if (!preg_match('/\d/', $password)) {
            $errors[] = 'Password harus memuat angka.';
        }
        if (!preg_match('/[\x21-\x2F\x3A-\x40\x5B-\x60\x7B-\x7E]/', $password)) {
            $errors[] = 'Password harus memuat karakter khusus (misalnya ! @ # $ %).';
        }
        if ($username !== null && $username !== '' && strcasecmp($password, $username) === 0) {
            $errors[] = 'Password tidak boleh sama dengan username.';
        }
        if ($password === self::DEFAULT_RESET) {
            $errors[] = 'Password tidak boleh memakai password default.';
        }

        return $errors;
    }
}
