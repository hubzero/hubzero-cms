<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;
use Hubzero\Database\Traits\Lockable;

/**
 * Test model with custom column names
 */
class CustomLockableArticle extends Relational
{
    use Lockable;

    protected $table = 'lockable_custom_articles';
    /**
     * @var  string
     */
    protected $checkedOutColumn = 'locked_by';
    /**
     * @var  string
     */
    protected $checkedOutTimeColumn = 'locked_at';
}
