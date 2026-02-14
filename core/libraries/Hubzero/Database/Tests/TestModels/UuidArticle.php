<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;
use Hubzero\Database\Traits\HasUuid;

/**
 * Test model with HasUuid trait and default column name
 */
class UuidArticle extends Relational
{
    use HasUuid;

    protected $table = 'uuid_articles';
    protected $pk = 'id';
    protected $namespace = '';
    protected $dispatchesModelEvents = true;
}
