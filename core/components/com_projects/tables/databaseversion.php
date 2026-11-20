<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;

/**
 * Table class for project database versions
 *
 * @property  mixed  $data_definition
 * @property  mixed  $database_name
 * @property  mixed  $version
 */
class DatabaseVersion extends Table
{
    /**
     * Constructor
     *
     * @param   \Hubzero\Database\Driver  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__project_database_versions', 'id', $db);
    }

    /**
     * Get max version number
     *
     * @param    string|null  $dbname
     * @return   mixed   integer or NULL
     */
    public function getMaxVersion($dbname = '')
    {
        if ($dbname === null) {
            return false;
        }

        $query = "SELECT MAX(version) as version FROM $this->tableName
					WHERE database_name=" . $this->db->quote($dbname);

        $this->db->setQuery($query);
        return $this->db->loadResult();
    }
}
