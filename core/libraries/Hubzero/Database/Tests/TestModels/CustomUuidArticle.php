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
 * Test model with HasUuid trait and custom column name
 */
class CustomUuidArticle extends Relational
{
    use HasUuid;

    protected $table = 'uuid_custom_articles';
    protected $pk = 'id';
    /**
     * @var  string
     */
    protected $uuidColumn = 'public_id';
    protected $namespace = '';
    protected $dispatchesModelEvents = true;
}
