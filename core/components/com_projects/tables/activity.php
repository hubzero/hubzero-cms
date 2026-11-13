<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;

/**
 * Table class for project activity
 */
class Activity extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__project_activity', 'id', $db);
    }

    /**
     * Load a record and bind to $this
     *
     * @param   integer  $id         activity id
     * @param   integer  $projectid
     * @return  mixed    object or false
     */
    public function loadActivity($id = null, $projectid = null)
    {
        if ($id === null || !intval($id) || $projectid === null) {
            return false;
        }
        $this->db->setQuery(
            "SELECT * FROM $this->tableName WHERE id="
                . intval($id)
                . " AND projectid="
                . intval($projectid)
                . " LIMIT 1",
        );
        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        } else {
            return false;
        }
    }

    /**
     * Load activity by reference
     *
     * @param   integer  $projectid
     * @param   integer  $refid
     * @param   string   $class
     * @param   string   $activity
     * @return  mixed    object or false
     */
    public function loadActivityByRef($projectid = null, $refid = 0, $class = '', $activity = '')
    {
        if ($projectid === null || $refid == 0 || !$class || !$activity || intval($projectid) == 0) {
            return false;
        }

        $query = "SELECT * FROM $this->tableName WHERE referenceid = '$refid'
				AND projectid = " . $this->db->quote($projectid) . " AND class = "
                . $this->db->quote($class) . " AND activity = "
                . $this->db->quote($activity) . " LIMIT 1";
        $this->db->setQuery($query);
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
     * @param   boolean|int $count
     * @param   integer  $uid
     * @param   array    $projects
     * @return  mixed    object or integer or false
     */
    public function getActivities($projectid = null, $filters = [], $count = 0, $uid = 0, $projects = [])
    {
        if (!$projectid && empty($projects)) {
            return false;
        }

        $sortby     = isset($filters['sortby']) ? $filters['sortby'] : 'recorded';
        $limit      = isset($filters['limit']) ? $filters['limit'] : 0;
        $limitstart = isset($filters['start']) ? $filters['start'] : 0;
        $class      = isset($filters['class']) ? $filters['class'] : '';
        $sortdir    = isset($filters['sortdir']) && strtoupper($filters['sortdir']) == 'ASC' ? 'ASC' : 'DESC';
        $managers   = isset($filters['managers']) ? $filters['managers'] : 0;
        $role       = isset($filters['role']) ? $filters['role'] : 0;
        $id         = isset($filters['id']) ? $filters['id'] : 0;

        $query   =  "SELECT ";
        if ($count) {
            $query  .=  " COUNT(*) ";
        } else {
            $query .= " DISTINCT a.*, x.name, x.username ";
        }
        $query  .= " FROM $this->tableName AS a ";
        if (!$count) {
            $query  .= "JOIN #__xprofiles as x ON x.uidNumber=a.userid";
        }
        if ($projectid) {
            $query  .= " WHERE a.projectid=" . $this->db->quote($projectid);
        } else {
            $query  .= " WHERE a.projectid IN (";
            $tquery = '';
            foreach ($projects as $project) {
                $tquery .= "'" . intval($project) . "',";
            }
            $tquery = substr($tquery, 0, strlen($tquery) - 1);
            $query .= $tquery . ") ";
        }
        if ($class) {
            $query  .= " AND a.class=" . $this->db->quote($class);
        }
        if ($managers && $role == 1) {
            $query  .= " AND a.managers_only=1 ";
        }
        if ($role == 0) {
            $query  .= " AND a.managers_only=0 ";
        }
        if ($id) {
            if (is_array($id)) {
                $query  .= " AND a.id IN (";
                $tquery = '';
                foreach ($id as $a) {
                    $tquery .= "'" . intval($a) . "',";
                }
                $tquery = substr($tquery, 0, strlen($tquery) - 1);
                $query .= $tquery . ") ";
            } elseif (intval($id)) {
                $query  .= " AND a.id=" . $this->db->quote($id);
            }
        }

        $query  .= " AND a.state != 2 ";

        if (isset($filters['recorded']) && $filters['recorded']) {
            $query  .= " AND a.recorded > " . $this->db->quote($filters['recorded']);
        }

        $query  .= " ORDER BY ";
        $query  .=  $sortby == 'recorded' ? " a.recorded $sortdir " : "";
        $query  .=  $sortby == 'class' ? " a.class $sortdir " : "";

        if (!$count) {
            if ($limit != 0) {
                $query .= " LIMIT " . (int) $limitstart . ", " . (int) $limit;
            }
        }

        $this->db->setQuery($query);
        return $count ? $this->db->loadResult() : $this->db->loadObjectList();
    }

    /**
     * Record activity
     *
     * @param   integer  $projectid
     * @param   integer  $by
     * @param   string   $activity
     * @param   string|int $referenceid
     * @param   string   $highlighted
     * @param   string   $url
     * @param   string   $class
     * @param   boolean|int $commentable
     * @param   boolean|int $admin
     * @param   boolean|int $managers_only
     * @return  integer|false  (activity id) or false
     */
    public function recordActivity(
        $projectid = null,
        $by = null,
        $activity = null,
        $referenceid = 0,
        $highlighted = '',
        $url = '',
        $class = 'project',
        $commentable = 0,
        $admin = 0,
        $managers_only = 0,
    ) {
        if ($projectid === null || $activity === null || $by === null || intval($projectid) == 0) {
            return false;
        }

        // Collapse some repeated activities by the same actor
        if ($referenceid || $class == 'project') {
            $this->db->setQuery("UPDATE $this->tableName SET state = 2 WHERE class="
                . $this->db->quote($class) . " AND activity="
                . $this->db->quote($activity) . " AND userid="
                . $this->db->quote($by) . " AND projectid="
                . $this->db->quote($projectid) . " AND referenceid="
                . $this->db->quote($referenceid));
            $this->db->query();
        }

        $this->commentable   = $commentable;
        $this->admin         = $admin;
        $this->managers_only = $managers_only;

        // Collapse checked/posted to-do item activities
        if ($class == 'todo' && $activity == Lang::txt('COM_PROJECTS_ACTIVITY_TODO_COMPLETED')) {
            $this->loadActivityByRef($projectid, $referenceid, $class, Lang::txt('COM_PROJECTS_ACTIVITY_TODO_ADDED'));
        }

        $this->projectid   = $projectid;
        $this->userid      = $by;
        $this->recorded    = Date::toSql();
        $this->activity    = $activity;
        $this->highlighted = $highlighted;
        $this->referenceid = $referenceid;
        $this->url         = $url;
        $this->class       = $class;

        if (!$this->store()) {
            return false;
        } else {
            return $this->id;
        }
    }

    /**
     * Delete activity by reference
     *
     * @param   integer  $projectid
     * @param   string|int $refid
     * @param   string   $class
     * @param   boolean  $permanent
     * @return  boolean  true on success
     */
    public function deleteActivityByReference($projectid = 0, $refid = 0, $class = '', $permanent = false)
    {
        if (!$refid || !$projectid || !$class || intval($projectid) == 0) {
            return false;
        }

        $query  = ($permanent) ? "DELETE FROM $this->tableName " : "UPDATE $this->tableName SET state = 2 ";
        $query .= " WHERE projectid="
            . $this->db->quote($projectid)
            . " AND referenceid="
            . $this->db->quote($refid)
            . " AND class="
            . $this->db->quote($class);

        $this->db->setQuery($query);
        if (!$this->db->query()) {
            return false;
        }
        return true;
    }

    /**
     * Delete activity
     *
     * @param   integer  $aid
     * @param   boolean  $permanent
     * @return  boolean  true on success
     */
    public function deleteActivity($aid = 0, $permanent = false)
    {
        if (!$aid) {
            $aid = $this->id;
        }
        if (!$aid || intval($aid) == 0) {
            return false;
        }

        $query  = ($permanent) ? "DELETE FROM $this->tableName " : "UPDATE $this->tableName SET state = 2 ";
        $query .= " WHERE id=" . (int) $aid;

        $this->db->setQuery($query);
        if (!$this->db->query()) {
            return false;
        }
        return true;
    }

    /**
     * Save activity preview
     *
     * @param   integer  $aid
     * @param   string   $preview
     * @return  boolean  true on success
     */
    public function saveActivityPreview($aid = 0, $preview = null)
    {
        if (!$aid || intval($aid) == 0) {
            return false;
        }
        $query  = "UPDATE $this->tableName SET preview =" . $this->db->quote($preview);
        $query .= " WHERE id=" . (int) $aid;

        $this->db->setQuery($query);
        if (!$this->db->query()) {
            return false;
        }
        return true;
    }

    /**
     * Delete activities
     *
     * @param   integer  $projectid
     * @param   boolean  $permanent
     * @return  boolean  true on success
     */
    public function deleteActivities($projectid = 0, $permanent = false)
    {
        if (!$projectid) {
            $projectid = $this->projectid;
        }
        if (!$projectid || intval($projectid) == 0) {
            return false;
        }

        $query  = ($permanent) ? "DELETE FROM $this->tableName " : "UPDATE $this->tableName SET state = 2 ";
        $query .= " WHERE projectid=" . (int) $projectid;

        $this->db->setQuery($query);
        if (!$this->db->query()) {
            return false;
        }
        return true;
    }

    /**
     * Get count of new activity since member last visit (single project)
     *
     * @param   integer  $projectid
     * @param   integer  $uid
     * @return  mixed    integer or null
     */
    public function getNewActivityCount($projectid = null, $uid = 0)
    {
        if ($projectid === null || !$uid || intval($projectid) == 0) {
            return false;
        }

        $query  = " SELECT COUNT(*) FROM `#__project_activity` AS X ";
        $query .= " LEFT JOIN #__project_owners as o ON o.projectid=X.projectid AND o.userid=" . $this->db->quote($uid);
        $query .= " WHERE X.projectid=" . (int) $projectid . "
					AND (X.recorded >= o.lastvisit AND o.lastvisit IS NOT null
					AND X.state != 2 AND (X.managers_only = 0
					OR (X.managers_only=1 AND o.role=1)))";

        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Get activity count
     *
     * @param   integer  $projectid
     * @param   integer  $uid
     * @return  mixed    integer or null
     */
    public function getActivityCount($projectid = null, $uid = 0)
    {
        if ($projectid === null || !$uid || intval($projectid) == 0) {
            return false;
        }

        $query  = " SELECT COUNT(*) FROM `#__project_activity` AS X ";
        $query .= " WHERE X.projectid=" . (int) $projectid . "
				    AND X.state != 2";

        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Get top active projects
     *
     * @param   array    $exclude
     * @param   integer  $limit
     * @param   boolean  $publicOnly
     * @return  array
     */
    public function getTopActiveProjects($exclude = [], $limit = 3, $publicOnly = false)
    {
        $query  = " SELECT p.id, p.alias, p.title, p.picture, p.private, COUNT(PA.id) as activity ";
        $query .= " FROM #__projects AS p";
        $query .= " JOIN $this->tableName as PA ON PA.projectid = p.id WHERE PA.projectid = p.id ";

        if ($publicOnly) {
            $query .= " AND p.private = 0 ";
        }

        if (!empty($exclude)) {
            $query .= " AND p.id NOT IN (";

            $tquery = '';
            foreach ($exclude as $ex) {
                $tquery .= "'" . intval($ex) . "',";
            }
            $tquery = substr($tquery, 0, strlen($tquery) - 1);
            $query .= $tquery . ") ";
        }

        $query .= " GROUP BY p.id ";
        $query .= " ORDER BY activity DESC ";
        $query .= " LIMIT 0," . intval($limit);

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Get activity stats
     *
     * @param   array   $validProjects
     * @param   string  $get
     * @return  mixed
     */
    public function getActivityStats($validProjects = [], $get = 'total')
    {
        if (empty($validProjects)) {
            return null;
        }

        $query  = " SELECT COUNT(*) as activity ";
        $query .= " FROM $this->tableName ";

        $query .= " WHERE projectid IN (";

        $tquery = '';
        foreach ($validProjects as $v) {
            $tquery .= "'" . intval($v) . "',";
        }
        $tquery = substr($tquery, 0, strlen($tquery) - 1);
        $query .= $tquery . ") ";

        if ($get == 'average') {
            $query .= " GROUP BY projectid ";
        }

        $this->db->setQuery($query);

        if ($get == 'total') {
            return $this->db->loadResult();
        } elseif ($get == 'average') {
            $result = $this->db->loadObjectList();

            $c = 0;
            $d = 0;

            foreach ($result as $r) {
                $c = $c + $r->activity;
                $d++;
            }

            return $d ? number_format($c / $d, 0) : 0;
        }
    }

    /**
     * Match activity
     *
     * @param   integer  $projectid
     * @param   string   $check
     * @return  mixed    integer or false
     */
    public function checkActivity($projectid = null, $check = null)
    {
        if ($projectid === null || intval($projectid) == 0) {
            return false;
        }

        $query  = "SELECT IF(admin = 0, 2, 1) FROM $this->tableName ";
        $query .= "WHERE projectid=" . $this->db->quote($projectid);
        $query .= " AND activity=" . $this->db->quote($check) . " AND state!=2 ";
        $query .= " ORDER BY recorded DESC LIMIT 1";
        $this->db->setQuery($query);
        $result = $this->db->loadResult();
        if (!$result) {
            return false;
        } else {
            return $result;
        }
    }
}
