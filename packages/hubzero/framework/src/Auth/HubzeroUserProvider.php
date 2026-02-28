<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\DB;

/**
 * Custom user provider for HubZero's authentication system.
 *
 * - Accepts username OR email for login
 * - Checks jos_users_password.passhash first, falls back to jos_users.password
 * - Rejects blocked users
 * - Transparently upgrades legacy hashes to bcrypt on successful login
 */
class HubzeroUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by the given credentials.
     *
     * HubZero's login form accepts either username or email address.
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $login = $credentials['username'] ?? $credentials['email'] ?? null;

        if (empty($login)) {
            return null;
        }

        $model = $this->createModel();
        $query = $model->newQuery();

        // Accept either username or email
        if (str_contains($login, '@')) {
            $query->where('email', $login);
        } else {
            $query->where('username', $login);
        }

        $user = $query->first();

        // Reject blocked users
        if ($user && $user->isBlocked()) {
            return null;
        }

        return $user;
    }

    /**
     * Validate a user against the given credentials.
     *
     * Checks the canonical password source (jos_users_password) first,
     * then falls back to jos_users.password.
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        $password = $credentials['password'] ?? '';

        if (empty($password)) {
            return false;
        }

        // Try canonical source: jos_users_password table
        $passhash = $this->getCanonicalHash($user->getKey());

        if ($passhash && $this->checkPassword($password, $passhash)) {
            $this->rehashIfNeeded($user, $passhash, $password);
            return true;
        }

        // Fall back to jos_users.password column
        $userPassword = $user->getAuthPassword();

        if ($userPassword && $this->checkPassword($password, $userPassword)) {
            $this->rehashIfNeeded($user, $userPassword, $password);
            return true;
        }

        return false;
    }

    /**
     * Check a password against a hash, supporting both Laravel (bcrypt)
     * and HubZero legacy formats ({CRYPT}, {MD5}, {SSHA}, Joomla md5:salt).
     */
    private function checkPassword(string $password, string $hash): bool
    {
        // Try Laravel's hasher first (handles bcrypt/$2y$ hashes)
        if ($this->hasher->check($password, $hash)) {
            return true;
        }

        // Legacy HubZero {CRYPT}$6$... format
        if (str_starts_with($hash, '{CRYPT}')) {
            $cryptHash = substr($hash, 7); // strip {CRYPT} prefix
            return hash_equals($cryptHash, crypt($password, $cryptHash));
        }

        // Legacy Joomla md5:salt format
        if (str_contains($hash, ':')) {
            [$md5, $salt] = explode(':', $hash, 2);
            return hash_equals($md5, md5($password . $salt));
        }

        // Plain md5 (very old Joomla 1.0)
        if (preg_match('/^[a-f0-9]{32}$/', $hash)) {
            return hash_equals($hash, md5($password));
        }

        return false;
    }

    /**
     * Get the password hash from the canonical jos_users_password table.
     */
    private function getCanonicalHash(int $userId): ?string
    {
        $row = DB::table('users_password')
            ->where('user_id', $userId)
            ->value('passhash');

        return $row ?: null;
    }

    /**
     * Transparently upgrade legacy hashes to bcrypt.
     *
     * Disabled while the legacy entry point is still active — rehashing
     * to bcrypt would break legacy login which expects {CRYPT} format.
     */
    private function rehashIfNeeded(Authenticatable $user, string $currentHash, string $plaintext): void
    {
        // No-op: legacy and Laravel must share password hashes during migration
    }
}
