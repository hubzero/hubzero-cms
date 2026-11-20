<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function item()
    {
        return $this->belongsToOne(RefreshTestItem::class, 'item_id');
    }
}
