<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;

/**
 * Table class for project to-do's
 */
class Todo extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__project_todo', 'id', $db);
    }

    /**
     * Load a record and bind to $this
     *
     * @param   string   $projectid
     * @param   integer  $id
     * @return  boolean  False or object
     */
    public function loadTodo($projectid = null, $id = 0)
    {
        if ($projectid == null or $id == 0) {
            return false;
        }

        $query = "SELECT * FROM $this->tableName"
            . " WHERE projectid=" . $this->db->quote($projectid)
            . " AND id=" . $this->db->quote($id)
            . " LIMIT 1";
        $this->db->setQuery($query);

        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        } else {
            $this->setError($this->db->getErrorMsg());
            return false;
        }
    }

    /**
     * Get records
     *
     * @param   integer  $projectid
     * @param   array    $filters
     * @param   integer  $id
     * @return  mixed    integer or null
     */
    public function getTodos($projectid = null, $filters = [], $id = 0)
    {
        $projects = isset($filters['projects']) ? $filters['projects'] : [$projectid];

        if (empty($projects)) {
            return false;
        }

        $count      = isset($filters['count']) ? $filters['count'] : 0;
        $limit      = isset($filters['limit']) ? $filters['limit'] : 0;
        $limitstart = isset($filters['start']) ? $filters['start'] : 0;
        $color      = isset($filters['todolist']) ? $filters['todolist'] : '';
        $assignedto = isset($filters['assignedto']) ? $filters['assignedto'] : '';
        $state      = isset($filters['state']) ? intval($filters['state']) : 0;
        $activityid = isset($filters['activityid']) ? intval($filters['activityid']) : 0;

        $query  = "SELECT ";
        if ($count) {
            $query .= " COUNT(*) ";
        } else {
            $query .= "p.*, xp.name AS authorname, xpp.name AS assignedname, "
                . "xppp.name AS closedbyname, "
                . "IF (p.duedate ='0000-00-00 00:00:00' OR p.duedate IS null, 0, 1) as due ";
            $query .= ", (SELECT COUNT(*) FROM #__project_comments as c "
                . "WHERE c.itemid=p.id AND c.tbl='todo' AND c.state!=2) as comments ";
        }
        $query .= "FROM $this->tableName AS p  ";
        if (!$count) {
            $query .= "JOIN #__users AS xp ON xp.id=p.created_by ";
            $query .= "LEFT JOIN #__users AS xpp ON xpp.id=p.assigned_to ";
            $query .= "LEFT JOIN #__users AS xppp ON xppp.id=p.closed_by ";
        }

        foreach ($projects as $k => $project) {
            $projects[$k] = $this->db->quote($project);
        }
        $query .= " WHERE p.projectid IN (" . implode(',', $projects) . ") ";

        if ($id) {
            $query .= " AND p.id = " . $this->db->quote($id) . " ";
        } else {
            $query .= $color ? " AND p.color=" . $this->db->quote($color) : " ";
            $query .= $assignedto ? " AND p.assigned_to=" . $this->db->quote($assignedto) : " ";
            $query .= " AND p.state=" . $this->db->quote($state);
            if ($activityid) {
                $query .= " AND p.activityid=" . $this->db->quote($activityid);
            }
        }

        if (!$count) {
            $sort = '';
            $sortby  = isset($filters['sortby']) ? $filters['sortby'] : 'priority';
            $sortdir = isset($filters['sortdir']) && strtoupper($filters['sortdir']) == 'DESC' ? 'DESC' : 'ASC';

            switch ($sortby) {
                case 'due':
                    $sort .= 'due DESC, p.duedate ' . $sortdir;
                    break;

                case 'complete':
                    $sort .= 'p.closed ' . $sortdir;
                    break;

                case 'list':
                    $sort .= 'p.color ' . $sortdir;
                    break;

                case 'content':
                    $sort .= 'p.content ' . $sortdir;
                    break;

                case 'project':
                    $sort .= 'p.projectid ' . $sortdir;
                    break;

                case 'priority':
                default:
                    $sort .= 'p.priority ' . $sortdir;
                    break;
            }

            $query .= "ORDER BY $sort ";
            if ($limit != 0) {
                $query .= " LIMIT " . (int) $limitstart . ", " . (int) $limit;
            }
        }

        $this->db->setQuery($query);
        return $count ? $this->db->loadResult() : $this->db->loadObjectList();
    }

    /**
     * Get lists
     *
     * @param   integer  $projectid
     * @param   array    $filters
     * @return  mixed    object or null
     */
    public function getTodoLists($projectid = null, $filters = [])
    {
        if ($projectid == null) {
            return false;
        }

        $query  = "SELECT ";
        $query .= isset($filters['count']) && $filters['count'] == 1 ? " COUNT(*) " : "DISTINCT todolist, color ";
        $query .= "FROM $this->tableName ";
        $query .= "WHERE projectid =" . $this->db->quote($projectid)
            . " AND todolist IS NOT null AND color IS NOT null"
            . " AND todolist != '' AND color != '' ";
        $query .= "ORDER BY todolist";
        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Get list name by color
     *
     * @param   integer  $projectid
     * @param   string   $color
     * @return  mixed    string or null
     */
    public function getListName($projectid = null, $color = '')
    {
        if ($projectid == null or $color == '') {
            return false;
        }

        $query  = "SELECT todolist FROM $this->tableName ";
        $query .= "WHERE projectid =" . $this->db->quote($projectid) . " AND color = " . $this->db->quote($color);
        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Delete list
     *
     * @param   integer  $projectid
     * @param   string   $color
     * @param   boolean  $all
     * @param   boolean  $permanent
     * @return  boolean  True on success
     */
    public function deleteList($projectid = null, $color = '', $all = 0, $permanent = 0)
    {
        if ($projectid == null or $color == '') {
            return false;
        }
        $projectidQuoted = $this->db->quote($projectid);
        $colorQuoted = $this->db->quote($color);
        if ($all) {
            $query = "DELETE FROM $this->tableName"
                . " WHERE projectid =" . $projectidQuoted
                . " AND color = " . $colorQuoted;
        } else {
            $query = "UPDATE $this->tableName SET color = '', todolist = ''"
                . " WHERE projectid =" . $projectidQuoted
                . " AND color =" . $colorQuoted;
        }

        $this->db->setQuery($query);
        if (!$this->db->query()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }
        return true;
    }

    /**
     * Get last item order
     *
     * @param   integer  $projectid
     * @return  integer
     */
    public function getLastOrder($projectid = null)
    {
        if ($projectid === null) {
            return false;
        }

        $query  = "SELECT priority FROM $this->tableName ";
        $query .= "WHERE projectid =" . $this->db->quote($projectid) . " ORDER BY priority DESC LIMIT 1 ";
        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Delete items
     *
     * @param   integer  $projectid
     * @param   string   $todolist
     * @param   boolean  $permanent
     * @return  void
     */
    public function deleteTodos($projectid, $todolist = '', $permanent = 0)
    {
        if ($projectid == null) {
            return false;
        }
        if ($permanent) {
            $query = "DELETE FROM $this->tableName WHERE projectid=" . $this->db->quote($projectid);
        } else {
            $query = "UPDATE $this->tableName SET state = 2 WHERE projectid =" . $this->db->quote($projectid);
        }
        $query .= $todolist ? " AND color=" . $this->db->quote($todolist) : "";

        $this->db->setQuery($query);
        $this->db->query();
    }

    /**
     * Delete item
     *
     * @param   integer  $projectid
     * @param   integer  $todoid
     * @param   boolean  $permanent
     * @return  boolean  True if success
     */
    public function deleteTodo($projectid, $todoid = 0, $permanent = 0)
    {
        if ($projectid == null) {
            return false;
        }

        if ($permanent) {
            $query = "DELETE FROM $this->tableName WHERE projectid=" . $this->db->quote($projectid);
        } else {
            $query = "UPDATE $this->tableName SET state = 2 WHERE projectid =" . $this->db->quote($projectid);
        }

        $query .= " AND id=" . $this->db->quote($todoid);
        $this->db->setQuery($query);
        $this->db->query();
        return true;
    }
}
