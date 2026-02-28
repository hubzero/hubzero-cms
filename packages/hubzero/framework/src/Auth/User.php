<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Auth;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Eloquent model for the jos_users table.
 *
 * Used by Laravel's auth system for session-based authentication.
 */
class User extends Authenticatable
{
    protected $table = 'users';

    protected $hidden = ['password'];

    public $timestamps = false;

    /**
     * Check if the user account is blocked.
     */
    public function isBlocked(): bool
    {
        return (bool) ($this->block ?? false);
    }
}
