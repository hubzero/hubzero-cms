<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;

/**
 * Table class for project types
 */
class Type extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__project_types', 'id', $db);
    }

    /**
     * Get params
     *
     * @param   integer  $type
     * @return  string   or null
     */
    public function getParams($type = 1)
    {
        $this->db->setQuery("SELECT params FROM $this->tableName WHERE id=" . (int) $type);
        return $this->db->loadResult();
    }

    /**
     * Get types
     *
     * @return  object  or null
     */
    public function getTypes()
    {
        $this->db->setQuery("SELECT * FROM $this->tableName");
        return $this->db->loadObjectList();
    }

    /**
     * Get type title
     *
     * @param   integer  $id
     * @return  string   or null
     */
    public function getTypeTitle($id = 0)
    {
        $this->db->setQuery("SELECT type FROM $this->tableName WHERE id=" . $this->db->quote($id));
        return $this->db->loadResult();
    }

    /**
     * Get ID by type title
     *
     * @param   string  $type
     * @return  string  or null
     */
    public function getIdByTitle($type = '')
    {
        $this->db->setQuery("SELECT id FROM $this->tableName WHERE type=" . $this->db->quote($type));
        return $this->db->loadResult();
    }
}
