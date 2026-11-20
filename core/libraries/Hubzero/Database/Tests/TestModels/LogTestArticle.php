<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
