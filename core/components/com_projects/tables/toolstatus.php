<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;

/**
 * Project tool status class
 */
class ToolStatus extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__project_tool_statuses', 'id', $db);
    }

    /**
     * Get statuses
     *
     */
    public function getItems()
    {
        $query  = "SELECT * FROM $this->tableName ORDER BY id ";

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }
}
