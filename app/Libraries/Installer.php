<?php

namespace App\Libraries;

/**
 * Logika murni untuk `php spark perpus:install` (tanpa database), agar mudah dites.
 */
class Installer
{
    /** Nilai pepper contoh yang dulu ada di env.example. Sudah publik, jadi tidak boleh dipakai di instalasi baru. */
    public const PUBLIC_PEPPERS = ['', 'R4nd0mStr1ng_SuperSecret_2026'];

    /**
     * Pepper baru hanya dibuat bila nilai sekarang kosong/publik DAN belum ada akun.
     * Bila sudah ada akun, mengganti pepper membuat password lama tidak bisa dipakai login.
     */
    public static function shouldGeneratePepper(?string $current, int $userCount): bool
    {
        return $userCount === 0 && in_array(trim((string) $current), self::PUBLIC_PEPPERS, true);
    }

    public static function isPublicPepper(?string $current): bool
    {
        return in_array(trim((string) $current), self::PUBLIC_PEPPERS, true);
    }

    public static function generatePepper(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Isi atau ganti satu kunci di teks .env. Baris yang dikomentari (# KEY=...) tidak diubah;
     * jika kunci belum ada, ditambahkan di akhir.
     */
    public static function setEnvValue(string $content, string $key, string $value): string
    {
        $pattern = '/^' . preg_quote($key, '/') . '\s*=.*$/m';
        $line    = $key . '=' . $value;

        if (preg_match($pattern, $content)) {
            return preg_replace($pattern, str_replace(['\\', '$'], ['\\\\', '\\$'], $line), $content, 1);
        }

        return rtrim($content, "\r\n") . "\n" . $line . "\n";
    }

    /** Ambil nilai kunci dari teks .env (null bila tidak ada). */
    public static function getEnvValue(string $content, string $key): ?string
    {
        if (preg_match('/^' . preg_quote($key, '/') . '\s*=(.*)$/m', $content, $m)) {
            return trim(trim($m[1]), "\"'");
        }

        return null;
    }
}
