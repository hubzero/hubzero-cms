<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test comment model for relationship tests
 */
class RefreshTestComment extends Relational
{
    protected $table = 'refresh_test_comments';
    protected $pk = 'id';

    /**
     * Belongs to item
     */
    public function item()
    {
        return $this->belongsToOne(RefreshTestItem::class, 'item_id');
    }
}
