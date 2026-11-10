<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\Date;

/**
 * Table class for project comments
 */
class Comment extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__project_comments', 'id', $db);
    }

    /**
     * Load user comment and bind to $this
     *
     * @param   integer  $itemid
     * @param   integer  $user_id
     * @return  mixed    object or false
     */
    public function loadUserComment($itemid = null, $user_id = null)
    {
        if ($itemid === null || $user_id === null) {
            return false;
        }

        $query = "SELECT * FROM $this->tableName WHERE itemid=" . $this->db->quote($itemid)
            . " AND created_by=" . $this->db->quote($user_id) . " LIMIT 1";
        $this->db->setQuery($query);
        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        } else {
            $this->setError($this->db->getErrorMsg());
            return false;
        }
    }

    /**
     * Load a record by id and bind to $this
     *
     * @param   integer  $commentid
     * @return  mixed    object or false
     */
    public function loadComment($commentid = null)
    {
        if ($commentid === null) {
            return false;
        }

        $this->db->setQuery("SELECT * FROM $this->tableName WHERE id=" . $this->db->quote($commentid) . " LIMIT 1");
        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        } else {
            $this->setError($this->db->getErrorMsg());
            return false;
        }
    }

    /**
     * Get items
     *
     * @param   integer  $itemid
     * @param   string   $tbl
     * @param   integer  $activityid
     * @param   string   $lastvisit
     * @param   integer  $parent_activity
     * @return  object
     */
    public function getComments($itemid = null, $tbl = 'blog', $activityid = 0, $lastvisit = 0, $parent_activity = 0)
    {
        if (!$itemid) {
            $itemid = $this->itemid;
        }
        if (!$itemid && !$activityid && !$parent_activity) {
            return false;
        }

        $query = "SELECT c.*, x.name as author ";
        if ($lastvisit && $activityid) {
            $query .= ", (SELECT count(*) from $this->tableName as cc WHERE cc.parent_activity = c.parent_activity"
                . " AND cc.created > " . $this->db->quote($lastvisit) . ") as newcount ";
        }
        $query .= " FROM $this->tableName as c";
        $query .= " JOIN #__xprofiles as x ON x.uidNumber=c.created_by ";
        if ($parent_activity) {
            $query .= " WHERE c.parent_activity=" . $this->db->quote($parent_activity);
        } else {
            if ($activityid) {
                $query .= " WHERE c.activityid=" . $this->db->quote($activityid);
            } else {
                $query .= " WHERE c.itemid=" . $this->db->quote($itemid)
                    . " AND c.tbl=" . $this->db->quote($tbl);
            }
        }
        $query .= " AND c.state != 2 ";
        $query .= " ORDER BY c.created ASC";

        $this->db->setQuery($query);
        $result = $this->db->loadObjectList();
        return $activityid && $result ? $result[0] : $result;
    }

    /**
     * Check if identical comment is made (prevents duplicates on multiple 'save' click)
     *
     * @param   integer  $uid
     * @param   string   $tbl
     * @param   integer  $itemid
     * @param   integer  $parent_activity
     * @param   string   $comment
     * @return  integer
     */
    public function checkDuplicate($uid = 0, $tbl = null, $itemid = 0, $parent_activity = 0, $comment = null)
    {
        $query = "SELECT id FROM $this->tableName WHERE created_by="
                . $this->db->quote($uid) . " AND itemid="
                . $this->db->quote($itemid) . " AND tbl="
                . $this->db->quote($tbl) . " AND parent_activity="
                . $this->db->quote($parent_activity) . " AND comment="
                . $this->db->quote($comment) . " AND state!=2 ";
        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Collect activity ids
     *
     * @param   integer  $itemid
     * @param   string   $tbl
     * @return  array
     */
    public function collectActivities($itemid = null, $tbl = 'blog')
    {
        if (!$itemid) {
            $itemid = $this->itemid;
        }
        if (!$itemid) {
            return false;
        }
        $activities = [];

        $query = "SELECT activityid as aid FROM $this->tableName WHERE itemid=" . $this->db->quote($itemid)
            . " AND tbl=" . $this->db->quote($tbl);
        $this->db->setQuery($query);
        $result = $this->db->loadObjectList();
        if ($result) {
            foreach ($result as $r) {
                $activities[] = $r->aid;
            }
        }
        return $activities;
    }

    /**
     * Save comment
     *
     * @param   integer  $itemid
     * @param   string   $tbl
     * @param   string   $comment
     * @param   integer  $by
     * @param   integer  $parent_activity
     * @param   integer  $admin
     * @return  mixed    (comment id) or false
     */
    public function addComment($itemid = null, $tbl = '', $comment = '', $by = 0, $parent_activity = 0, $admin = 0)
    {
        if (!$itemid || !$tbl || !$by || !$comment || !$parent_activity) {
            return false;
        }

        $comment = \Hubzero\Utility\Str::truncate($comment, 250);
        $comment = \Hubzero\Utility\Sanitize::stripAll($comment);

        $this->itemid          = $itemid;
        $this->tbl             = $tbl;
        $this->parent_activity = $parent_activity;
        $this->comment         = $comment;
        $this->admin           = $admin;
        $this->created         = Date::of('now')->toSql();
        $this->created_by      = $by;

        if (!$this->store()) {
            return false;
        } else {
            return $this->id;
        }
    }

    /**
     * Store comment activity id
     *
     * @param   integer  $id
     * @param   string   $activityid
     * @return  mixed
     */
    public function storeCommentActivityId($id = 0, $activityid = 0)
    {
        if (!intval($id) || !intval($activityid)) {
            return false;
        }

        $query = "UPDATE $this->tableName SET activityid =" . $this->db->quote($activityid)
            . " WHERE id =" . $this->db->quote($id);
        $this->db->setQuery($query);
        $this->db->query();
    }

    /**
     * Delete items
     *
     * @param   integer  $itemid
     * @param   string   $tbl
     * @param   boolean  $permanent
     * @return  boolean  True on success
     */
    public function deleteComments($itemid = null, $tbl = 'blog', $permanent = false)
    {
        if (!$itemid) {
            $itemid = $this->itemid;
        }
        if (!$itemid) {
            return false;
        }

        $query  = ($permanent) ? "DELETE FROM $this->tableName " : "UPDATE $this->tableName SET state = 2 ";
        $query .= " WHERE itemid=" . $this->db->quote($itemid) . " AND tbl=" . $this->db->quote($tbl);

        $this->db->setQuery($query);

        if (!$this->db->query()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }
        return true;
    }

    /**
     * Delete item
     *
     * @param   integer  $cid
     * @param   boolean  $permanent
     * @return  boolean  True on success
     */
    public function deleteComment($cid = 0, $permanent = false)
    {
        if (!$cid) {
            $cid = $this->id;
        }
        if (!$cid) {
            return false;
        }

        $query  = ($permanent) ? "DELETE FROM $this->tableName " : "UPDATE $this->tableName SET state = 2 ";
        $query .= " WHERE id=" . $this->db->quote($cid);

        $this->db->setQuery($query);
        if (!$this->db->query()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }
        return true;
    }

    /**
     * Delete all comments from project
     *
     * @param   integer  $projectid
     * @param   boolean  $permanent
     * @return  boolean  True on success
     */
    public function deleteProjectComments($projectid = 0, $permanent = 0)
    {
        if (!$projectid) {
            $projectid = $this->projectid;
        }
        if (!$projectid) {
            return false;
        }

        $query  = ($permanent)
            ? "DELETE c FROM $this->tableName as c INNER JOIN `#__project_activity` as a ON a.id=c.activityid "
            : "UPDATE $this->tableName as c INNER JOIN `#__project_activity` as a ON a.id=c.activityid  SET c.state = "
                . "2 ";
        $query .= " WHERE a.projectid=" . $this->db->quote($projectid);

        $this->db->setQuery($query);
        if (!$this->db->query()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }
        return true;
    }
}
