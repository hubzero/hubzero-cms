<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;

/**
 * Table class for project tool logs
 */
class ToolLog extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__project_tool_logs', 'id', $db);
    }

    /**
     * Get item history
     *
     *
     * @param   string   $parent_name
     * @param   integer  $parent_id
     * @param   integer  $instance_id
     * @param   array    $filters
     * @return  object   list
     */
    public function getHistory($parent_name = null, $parent_id = null, $instance_id = 0, $filters = [])
    {
        if ($parent_name === null) {
            $parent_name = $this->parent_name;
        }
        if ($parent_id === null) {
            $parent_id = $this->parent_id;
        }
        if ($instance_id === null) {
            $instance_id = $this->instance_id;
        }
        if (!$parent_id && !$parent_name && !$instance_id) {
            return false;
        }
        $status_changed = isset($filters['status_changed']) ? $filters['status_changed'] : 0;
        $sortby         = !empty($filters['sortby']) ? $filters['sortby'] : 'L.recorded';
        $sortdir        = !empty($filters['sortdir']) ? $filters['sortdir'] : 'DESC';
        $admin          = isset($filters['admin']) ? $filters['admin'] : 0;

        $query  = "SELECT L.*, x.name as actor_name, x.username as username, x.picture ";
        $query .= "FROM $this->tableName as L ";
        $query .= "JOIN #__xprofiles as x ON x.uidNumber=L.actor ";
        $query .= "WHERE ";
        if ($instance_id) {
            $query .= "L.instance_id=" . $this->db->quote($instance_id);
        } elseif ($parent_name) {
            $query .= "L.parent_name=" . $this->db->quote($parent_name);
        } elseif ($parent_id) {
            $query .= "L.parent_id=" . $this->db->quote($parent_id);
        }

        $query .= $status_changed ? ' AND L.status_changed = 1' : '';
        $query .= $admin
            ? ' AND (L.admin = 1 OR (L.admin = 0 AND access = 0 )) '
            : ' AND (L.admin = 0 OR (L.admin = 1 AND access = 0 )) ';
        $query .= " ORDER BY " . (preg_replace('/[^a-zA-Z0-9_,. ]/', '', (string) $sortby) ?: '1') . " " . (strtoupper((string) $sortdir) === 'ASC' ? 'ASC' : 'DESC');

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Get last update
     *
     * @param   integer  $instance_id
     * @param   string   $parent_name
     * @param   integer  $parent_id
     * @param   integer  $status_changed
     * @return  mixed    object or null
     */
    public function getLastUpdate($instance_id = 0, $parent_name = null, $parent_id = null, $status_changed = 1)
    {
        if ($parent_name === null) {
            $parent_name = $this->parent_name;
        }
        if ($parent_id === null) {
            $parent_id = $this->parent_id;
        }
        if ($instance_id === null) {
            $instance_id = $this->instance_id;
        }

        $query  = "SELECT L.*, x.name as actor_name, x.username as username ";
        $query .= "FROM $this->tableName as L ";
        $query .= "JOIN #__xprofiles as x ON x.uidNumber=L.actor ";
        $query .= "WHERE ";
        if ($instance_id) {
            $query .= "L.instance_id=" . $this->db->quote($instance_id);
        } elseif ($parent_name) {
            $query .= "L.parent_name=" . $this->db->quote($parent_name);
        } elseif ($parent_id) {
            $query .= "L.parent_id=" . $this->db->quote($parent_id);
        }

        $query .= $status_changed ? ' AND L.status_changed = 1' : '';
        $query .= " ORDER BY L.recorded DESC LIMIT 1";

        $this->db->setQuery($query);
        $result = $this->db->loadObjectList();
        return $result ? $result[0] : null;
    }

    /**
     * Update parent name
     *
     * @param   string   $parent_id
     * @param   string   $newname
     * @return  boolean  True on success
     */
    public function updateParentName($parent_id = null, $newname = null)
    {
        if ($newname === null || $parent_id === null) {
            return false;
        }

        $query = "UPDATE $this->tableName SET parent_name =" . $this->db->quote($newname)
                . " WHERE parent_id =" . $this->db->quote($parent_id);
        $this->db->setQuery($query);
        if ($this->db->query()) {
            return true;
        }

        return false;
    }

    /**
     * Get log
     *
     * @param   integer  $toolid  Project tool id
     * @param   integer  $aid     Activity id
     * @return  mixed    Return string or null
     */
    public function getLog($toolid = 0, $aid = 0)
    {
        if (!intval($toolid) || !intval($aid)) {
            return false;
        }

        $query  = "SELECT log ";
        $query .= "FROM $this->tableName ";
        $query .= "WHERE parent_id=" . $this->db->quote($toolid)
            . " AND project_activity_id=" . $this->db->quote($aid);

        $this->db->setQuery($query);
        return $this->db->loadResult();
    }
}
