<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Comment model for testing
 */
class CascadeComment extends Relational
{
    protected $table = 'cascade_comments';
    protected $namespace = '';
}
