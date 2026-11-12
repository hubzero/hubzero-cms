<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
