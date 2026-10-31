<?php

namespace App\Validation;

use App\Libraries\PasswordPolicy;

/**
 * Aturan validasi `strong_password` (lihat PasswordPolicy). Pemakaian:
 * 'strong_password' atau 'strong_password[username]' untuk ikut membandingkan dengan field username.
 */
class PasswordRules
{
    public function strong_password($value, ?string $usernameField = null, array $data = [], ?string &$error = null): bool
    {
        $username = $usernameField ? (string) ($data[$usernameField] ?? '') : null;
        $errors   = PasswordPolicy::errors((string) $value, $username);

        if ($errors === []) {
            return true;
        }

        $error = implode(' ', $errors);

        return false;
    }
}
