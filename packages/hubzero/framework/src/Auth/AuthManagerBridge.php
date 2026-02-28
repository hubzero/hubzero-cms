<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Auth;

use Exception;
use Illuminate\Support\Facades\Auth;

/**
 * Bridges the legacy App::get('auth')->login() API to Laravel's Auth system.
 *
 * The legacy com_users controller calls:
 *   App::get('auth')->login($credentials, $options)
 * where $credentials = ['username' => ..., 'password' => ...]
 *
 * This bridge translates that to Auth::attempt() so sessions are managed
 * by Laravel's session guard and the HubzeroUserProvider handles password
 * verification (including legacy hash formats).
 */
class AuthManagerBridge
{
    /**
     * Authenticate and log in a user.
     *
     * @param  array  $credentials  ['username' => string, 'password' => string]
     * @param  array  $options      ['remember' => bool, ...]
     * @return bool|Exception  True on success, Exception on failure
     */
    public function login(array $credentials, array $options = []): bool|Exception
    {
        $login = $credentials['username'] ?? '';
        $password = $credentials['password'] ?? '';

        if (empty($login) || empty($password)) {
            return new Exception('Username and password are required');
        }

        // Build Laravel credentials — accept username or email
        $creds = str_contains($login, '@')
            ? ['email' => $login, 'password' => $password]
            : ['username' => $login, 'password' => $password];

        $remember = !empty($options['remember']);

        if (Auth::attempt($creds, $remember)) {
            if (request()->hasSession()) {
                request()->session()->regenerate();
            }
            return true;
        }

        return new Exception('Invalid username or password');
    }

    /**
     * Log out a user.
     *
     * @param  int|null  $userid   Ignored — Laravel logs out the current user
     * @param  array     $options  Ignored
     * @return bool
     */
    public function logout($userid = null, $options = []): bool
    {
        Auth::logout();

        if (request()->hasSession()) {
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        return true;
    }
}
