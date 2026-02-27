<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $guarded = ['id'];

    public $timestamps = false;
}
