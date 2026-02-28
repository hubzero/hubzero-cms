<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Auth;

use Illuminate\Contracts\Hashing\Hasher;

/**
 * Password hasher that understands HubZero's legacy hash formats.
 *
 * Supports: {CRYPT}$6$ (SHA-512), {MD5} (base64), bcrypt ($2y$),
 * and legacy Joomla md5-hex (with optional salt).
 *
 * New passwords are hashed with bcrypt. Existing legacy hashes are
 * transparently upgraded to bcrypt on next successful login via
 * needsRehash().
 */
class HubzeroHasher implements Hasher
{
    public function info($hashedValue): array
    {
        return password_get_info($hashedValue);
    }

    public function make($value, array $options = []): string
    {
        return password_hash($value, PASSWORD_BCRYPT, $options);
    }

    public function check($value, $hashedValue, array $options = []): bool
    {
        if (empty($hashedValue) || empty($value)) {
            return false;
        }

        // Bcrypt hash — use native verification
        if (str_starts_with($hashedValue, '$2y$') || str_starts_with($hashedValue, '$2a$')) {
            return password_verify($value, $hashedValue);
        }

        // HubZero/Joomla format — parse {TYPE}hash or salt:hash
        return $this->compareLegacy($hashedValue, $value);
    }

    public function needsRehash($hashedValue, array $options = []): bool
    {
        // Anything that isn't bcrypt should be rehashed
        if (str_starts_with($hashedValue, '$2y$') || str_starts_with($hashedValue, '$2a$')) {
            return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, $options);
        }

        return true;
    }

    /**
     * Compare a plaintext password against a HubZero/Joomla legacy hash.
     *
     * Ported from Hubzero\User\Password::comparePasswords()
     */
    private function compareLegacy(string $passhash, string $password): bool
    {
        preg_match("/^\s*(\{(.*)\}\s*|)((.*?)\s*:\s*|)(.*?)\s*$/", $passhash, $matches);

        $encryption = strtolower($matches[2] ?? '');

        if (empty($encryption)) {
            // Joomla legacy: md5hex or md5hex:salt
            $encryption = 'md5-hex';
            if (!empty($matches[4])) {
                $crypt = $matches[4];
                $salt = $matches[5];
            } else {
                $crypt = $matches[5];
                $salt = '';
            }
        } else {
            $salt = $matches[4] ?? '';
            $crypt = $matches[5] ?? '';
        }

        if ($encryption === 'md5') {
            $encryption = 'md5-base64';
        } elseif ($encryption === 'crypt') {
            if (preg_match('/\$([[:alnum:]]{1,2})\$[[:alnum:]]{8}\$/', $passhash, $parts)) {
                $salt = $parts[0];
                $encryption = match ($parts[1]) {
                    '6' => 'crypt-sha512',
                    default => 'crypt-sha512',
                };
            }
        }

        $hashed = $this->getCryptedPassword($password, $salt, $encryption);

        return hash_equals($crypt, $hashed);
    }

    /**
     * Hash a password with the given algorithm and salt.
     *
     * Ported from Hubzero\User\Password::getCryptedPassword()
     */
    private function getCryptedPassword(string $plaintext, string $salt, string $encryption): string
    {
        return match ($encryption) {
            'crypt', 'crypt-sha512', 'crypt-des', 'crypt-md5', 'crypt-blowfish'
                => crypt($plaintext, $salt),
            'md5-base64'
                => base64_encode(hash('MD5', $plaintext, true)),
            'md5-hex'
                => $salt ? md5($plaintext . $salt) : md5($plaintext),
            'sha'
                => base64_encode(hash('SHA1', $plaintext, true)),
            default
                => md5($plaintext),
        };
    }
}
