<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;
use Hubzero\Database\Traits\HasSequenceId;

/**
 * Test model with HasSequenceId trait and default sequence name
 */
class SequenceItem extends Relational
{
    use HasSequenceId;

    protected $table = 'seq_items';
    protected $pk = 'id';
    protected $namespace = '';
}
