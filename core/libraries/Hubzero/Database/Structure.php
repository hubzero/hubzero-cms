<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

/**
 * Database structure class
 */
class Structure extends Query
{
    /**
     * Retrieves field information about the given tables
     *
     * @param   string  $table     The name of the database table
     * @param   bool    $typeOnly  True (default) to only return field types
     * @return  array<mixed>
     */
    public function getTableColumns($table, $typeOnly = true)
    {
        $columns = $this->query($this->syntax->getColumnsQuery($table), 'rows');

        return $this->syntax->normalizeColumns($columns, $typeOnly);
    }
}
