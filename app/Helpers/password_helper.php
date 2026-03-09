<?php

if (!function_exists('hashPassword')) {
    function hashPassword(string $password): string
    {
        $pepper = env('APP_PASSWORD_PEPPER');
        return password_hash($password . $pepper, PASSWORD_BCRYPT);
    }
}

if (!function_exists('verifyPassword')) {
    function verifyPassword(string $password, string $hash): bool
    {
        $pepper = env('APP_PASSWORD_PEPPER');
        return password_verify($password . $pepper, $hash);
    }
}
