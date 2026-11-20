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
 * Test model with UUID as the primary key
 */
class UuidToken extends Relational
{
    use HasUuid;

    protected $table = 'uuid_tokens';
    protected $pk = 'uuid';
    /**
     * @var  bool
     */
    protected $uuidAsPrimaryKey = true;
    protected $namespace = '';
    protected $dispatchesModelEvents = true;
}
