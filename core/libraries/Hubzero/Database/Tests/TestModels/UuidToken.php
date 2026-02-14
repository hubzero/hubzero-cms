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
 * Test model with UUID as the primary key
 */
class UuidToken extends Relational
{
    use HasUuid;

    protected $table = 'uuid_tokens';
    protected $pk = 'uuid';
    protected $uuidAsPrimaryKey = true;
    protected $namespace = '';
    protected $dispatchesModelEvents = true;
}
