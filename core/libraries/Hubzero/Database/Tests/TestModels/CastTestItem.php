<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Model with various cast types defined
 */
class CastTestItem extends Relational
{
    protected $table = 'cast_test_items';
    protected $namespace = '';

    protected $casts = [
        'is_active'    => 'boolean',
        'view_count'   => 'integer',
        'price'        => 'float',
        'amount'       => 'decimal:2',
        'tax_rate'     => 'decimal:4',
        'settings'     => 'array',
        'metadata'     => 'object',
        'tags'         => 'collection',
        'published_at' => 'datetime',
        'created_date' => 'date',
        'expires_at'   => 'timestamp',
    ];
}
