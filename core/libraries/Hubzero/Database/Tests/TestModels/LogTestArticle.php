<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test model for query logging tests
 */
class LogTestArticle extends Relational
{
    protected $table = 'log_test_articles';
    protected $namespace = '';
}
