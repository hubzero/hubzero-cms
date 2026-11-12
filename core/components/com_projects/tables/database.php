<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;

/**
 * Table class for project databases
 */
class Database extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__project_databases', 'id', $db);
    }

    /**
     * Load a record and bind to $this
     *
     * @param   string  $identifier  database name or id
     * @return  mixed   object or false
     */
    public function loadRecord($identifier = null)
    {
        if ($identifier === null) {
            return false;
        }
        $name = is_numeric($identifier) ? 'id' : 'database_name';

        $this->db->setQuery("SELECT * FROM $this->tableName WHERE $name=" . $this->db->quote($identifier) . " LIMIT 1");
        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        } else {
            return false;
        }
    }

    /**
     * Get items
     *
     * @param   integer  $projectid
     * @param   array    $filters
     * @param   boolean  $skipPublished
     * @return  mixed    object, integer or null
     */
    public function getItems($projectid = null, $filters = [], $skipPublished = false)
    {
        if ($projectid == null) {
            return false;
        }

        $count  = isset($filters['count']) ? $filters['count'] : 0;

        $query  = "SELECT ";
        $query .= $count ? " COUNT(*) " : "*";
        $query .= " FROM $this->tableName ";
        $query .= " WHERE project = " . $this->db->quote($projectid);
        $query .= $skipPublished ? " AND revision IS null" : "";

        $this->db->setQuery($query);
        return $count ? $this->db->loadResult() : $this->db->loadObjectList();
    }

    /**
     * Get items
     *
     * @param   integer  $projectid
     * @param   array    $id
     * @return  mixed    object, integer or null
     */
    public function getResource($projectid = null, $id = null)
    {
        if ($projectid == null || $id == null) {
            return false;
        }

        $query = "SELECT database_name, title FROM $this->tableName WHERE id="
            . $this->db->quote($id) . " AND project=" . $this->db->quote($projectid);

        $this->db->setQuery($query);
        return $this->db->loadAssoc();
    }

    /**
     * Get database list
     *
     * @param   integer  $projectid
     * @return  mixed    object, integer or null
     */
    public function getList($projectid = null)
    {
        if ($projectid == null) {
            return false;
        }

        $query = "SELECT db.id, db.project, db.database_name, db.title, db.source_file,
				db.source_dir, db.source_revision, db.description, db.data_definition,
				db.revision, db.created, db.created_by, c.name
				FROM $this->tableName AS db LEFT JOIN `#__users` AS c ON (c.id=db.created_by)
				WHERE project = " . $this->db->quote($projectid) . " ORDER BY db.created DESC";

        $this->db->setQuery($query);
        return $this->db->loadAssocList();
    }

    /**
     * Get used items
     *
     * @param   integer  $projectid
     * @return  mixed    object, integer or null
     */
    public function getUsedItems($projectid = null)
    {
        if ($projectid == null) {
            return false;
        }

        $query = "SELECT DISTINCT IF(source_dir != '', CONCAT(source_dir, '/', source_file), source_file) as file
				  FROM $this->tableName
				  WHERE project = " . $this->db->quote($projectid);

        $this->db->setQuery($query);
        return $this->db->loadColumn();
    }
}
