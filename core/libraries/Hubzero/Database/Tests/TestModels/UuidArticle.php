<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
