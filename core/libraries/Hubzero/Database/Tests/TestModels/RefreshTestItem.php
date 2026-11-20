<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test model for refresh/fresh tests
 *
 * @property-read  mixed  $comments  What comments() gives
 */
class RefreshTestItem extends Relational
{
    protected $table = 'refresh_test_items';
    protected $pk = 'id';

    /**
     * Has many comments relationship
     *
     * @return  \Hubzero\Database\Relationship\OneToMany
     */
    public function comments()
    {
        return $this->oneToMany(RefreshTestComment::class, 'item_id');
    }
}
