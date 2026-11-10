<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use Hubzero\Database\Table;

/**
 * A table of things with an id and a title, read from a driver that needs
 * no database
 */
class ThingTable extends Table
{
    /**
     * @param   object  $db  Anything that answers getTableColumns()
     * @return  void
     */
    public function __construct($db)
    {
        parent::__construct('#__things', 'id', $db);
    }
}
