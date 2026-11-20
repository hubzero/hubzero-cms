<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;
use Hubzero\Database\Traits\HasSequenceId;

/**
 * Test model with HasSequenceId trait and custom sequence name
 */
class CustomSequenceItem extends Relational
{
    use HasSequenceId;

    protected $table = 'seq_items';
    protected $pk = 'id';
    protected $namespace = '';
    /**
     * @var  string
     */
    protected $sequenceName = 'custom_item_seq';
    protected $dispatchesModelEvents = true;
}
