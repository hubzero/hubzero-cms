<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models;

use Hubzero\Database\Relational;

/**
 * Members password reset token database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Token extends Relational
{
    /**
     * The table namespace
     *
     * @var string
     */
    protected $namespace = 'xprofiles';

    /**
     * Automatically fillable fields
     *
     * @var array<int, string>
     */
    public $initiate = [
        'created',
    ];
}
