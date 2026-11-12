<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\User;

/**
 * Table class for middleware sessions
 */
class Session extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('session', 'sessnum', $db);
    }

    /**
     * Load a record and bind it to $this object
     *
     * @param   integer  $sess      Session number
     * @param   string   $username  User to delete for
     * @return  boolean  False if errors, True if success
     */
    public function load($sess = null, $username = null)
    {
        if ($sess == null) {
            $sess = $this->sessnum;
        }
        if ($sess === null) {
            return false;
        }

        $query = "SELECT * FROM $this->tableName WHERE sessnum=" . $this->db->quote($sess);

        if ($username) {
            $query .= " AND username=" . $this->db->quote($username);
        }

        $this->db->setQuery($query);
        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        } else {
            $this->setError($this->db->getErrorMsg());
            return false;
        }
    }

    /**
     * Take the hard timeout of the tool version the session runs
     *
     * Unless the middleware gave the session one already. Does nothing until
     * the session table has the column.
     *
     * @param   object  $version  The tool version, as a table or model
     * @return  void
     */
    public function inheritHardTimeout($version)
    {
        if (property_exists($this, 'hardTimeout') && empty($this->hardTimeout) && !empty($version->hardTimeout)) {
            $this->hardTimeout = (int) $version->hardTimeout;
        }
    }

    /**
     * Retrieve a session
     *
     * @param   string  $sess        Session number
     * @param   string  $authorized  Admin authorization?
     * @return  object|false
     */
    public function loadSession($sess = null, $authorized = null)
    {
        if ($sess == null) {
            $sess = $this->sessnum;
        }
        if ($sess === null) {
            return false;
        }

        $mv = new Viewperm($this->db);

        if ($authorized === 'admin') {
            $query = "SELECT * FROM $mv->tableName AS v JOIN $this->tableName AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.sessnum=" . $this->db->quote($sess) . "
					  LIMIT 1";
        } else {
            $query = "SELECT * FROM $mv->tableName AS v JOIN $this->tableName AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.sessnum=" . $this->db->quote($sess) . "
					  AND v.viewuser=" . $this->db->quote(User::get('username'));
        }

        $this->db->setQuery($query);
        return $this->db->loadObject();
    }

    /**
     * Check if a user owns a session
     *
     * @param   string  $sess        Session number
     * @param   string  $authorized  Admin authorization?
     * @return  object|false
     */
    public function checkSession($sess = null, $authorized = null)
    {
        if ($sess == null) {
            $sess = $this->sessnum;
        }
        if ($sess === null) {
            return false;
        }

        $mv = new Viewperm($this->db);

        if ($authorized === 'admin') {
            $query = "SELECT * FROM $mv->tableName AS v JOIN $this->tableName AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.sessnum=" . $this->db->quote($sess) . "
					  LIMIT 1";
        } else {
            // Note: this check is different from others.
            // Here, we check that the User::get('username') OWNS the session.
            $query = "SELECT * FROM $mv->tableName AS v JOIN $this->tableName AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.sessnum=" . $this->db->quote($sess) . "
					  AND s.username=" . $this->db->quote(User::get('username')) . "
					  AND v.viewuser=" . $this->db->quote(User::get('username'));
        }

        $this->db->setQuery($query);
        return $this->db->loadObject();
    }

    /**
     * Get a session count for a user, optionally restricted to a specific app
     *
     * @param   string   $username  Username to look for
     * @param   string   $appname   App to look for
     * @return  integer|false
     */
    public function getCount($username = null, $appname = null)
    {
        if ($username == null) {
            $username = $this->username;
        }
        if ($username === null) {
            return false;
        }

        $a = "";
        if ($appname) {
            $a = "AND s.appname=" . $this->db->quote($appname);
        }

        $mv = new Viewperm($this->db);

        $query = "SELECT COUNT(*) FROM $mv->tableName AS v JOIN $this->tableName AS s
				  ON v.sessnum = s.sessnum
				  WHERE v.viewuser=" . $this->db->quote($username)
            . " AND s.username=" . $this->db->quote($username) . " $a
				  ORDER BY s.start";

        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Get sessions for a user, optionally restricted to a specific app
     *
     * @param   string  $username    Username to look for
     * @param   string  $appname     App to look for
     * @param   string  $authorized  Admin authorization?
     * @return  array|false
     */
    public function getRecords($username = null, $appname = null, $authorized = null)
    {
        if ($username == null) {
            $username = $this->username;
        }
        if ($username === null) {
            return false;
        }

        $a = "";
        if ($appname) {
            $a = "AND s.appname=" . $this->db->quote($appname);
        }

        $mv = new Viewperm($this->db);

        if ($authorized === 'admin') {
            $query = "SELECT * FROM $this->tableName AS s ORDER BY s.start";
        } else {
            $query = "SELECT * FROM $mv->tableName AS v JOIN $this->tableName AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.viewuser=" . $this->db->quote($username) . " $a
					  ORDER BY s.start";
        }

        if (empty($this->db)) {
            return false;
        }

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Get the time left until the session times out
     *
     * @param   integer  $sess  Sess ID
     * @return  integer|false
     */
    public function getTimeout($sess)
    {
        if ($sess == null) {
            $sess = $this->sessnum;
        }
        if ($sess === null) {
            return false;
        }

        $mv = new View($this->db);
        $mj = new Job($this->db);

        $query = "SELECT timeout-TIME_TO_SEC(TIMEDIFF(NOW(), accesstime)) AS remaining
			FROM $this->tableName AS s
			LEFT JOIN $mv->tableName AS v ON s.sessnum = v.sessnum
			LEFT JOIN $mj->tableName AS j ON s.sessnum = j.sessnum
			WHERE viewid IS null AND jobid IS null
			AND s.sessnum=" . $this->db->quote($sess);

        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Build a query from filters
     *
     * @param   array   $filters  Filters to determien hwo to build query
     * @return  string  SQL
     */
    public function buildQuery($filters)
    {
        $mv = new Viewperm($this->db);

        if (isset($filters['count']) && $filters['count']) {
            $query = "SELECT COUNT(*) ";
        } else {
            $query = "SELECT * ";
        }
        $query .= " FROM $mv->tableName AS v
					JOIN $this->tableName AS s ON v.sessnum = s.sessnum";

        $where = [];
        if (isset($filters['username']) && $filters['username'] != '') {
            $where[] = "v.viewuser=" . $this->db->quote($filters['username']);
        }
        if (isset($filters['appname']) && $filters['appname'] != '') {
            $where[] = "s.appname=" . $this->db->quote($filters['appname']);
        }
        if (isset($filters['zone_id']) && $filters['zone_id']) {
            $where[] = "s.zone_id=" . $this->db->quote($filters['zone_id']);
        }
        if (isset($filters['exechost']) && $filters['exechost'] != '') {
            $where[] = "s.exechost=" . $this->db->quote($filters['exechost']);
        }
        if (count($where) > 0) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        if (!isset($filters['sort']) || $filters['sort'] == '') {
            $filters['sort'] = 'start';
        }
        if (!isset($filters['sort_Dir']) || $filters['sort_Dir'] == '') {
            $filters['sort_Dir'] = 'DESC';
        }
        if (!in_array(strtoupper($filters['sort_Dir']), ['ASC', 'DESC'])) {
            $filters['sort_Dir'] = 'DESC';
        }

        if ($filters['sort'] == "viewuser") {
            $query .= " ORDER BY v."
                . preg_replace('/[^a-zA-Z0-9_.]/', '', (string) $filters['sort'])
                . " "
                . $filters['sort_Dir'];
        } else {
            $query .= " ORDER BY s."
                . preg_replace('/[^a-zA-Z0-9_.]/', '', (string) $filters['sort'])
                . " "
                . $filters['sort_Dir'];
        }

        if (isset($filters['limit']) && $filters['limit'] != 0  && $filters['limit'] != 'all') {
            $query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
        }

        return $query;
    }

    /**
     * Get a record count
     *
     * @param   array   $filters  Filters to determien hwo to build query
     * @return  integer
     */
    public function getAllCount($filters = [])
    {
        $filters['limit']  = 0;
        $filters['count']  = true;
        $filters['sortby'] = '';

        $this->db->setQuery($this->buildQuery($filters));
        return $this->db->loadResult();
    }

    /**
     * Get records
     *
     * @param   array  $filters  Filters to determien hwo to build query
     * @return  array
     */
    public function getAllRecords($filters = [])
    {
        $this->db->setQuery($this->buildQuery($filters));
        return $this->db->loadObjectList();
    }

    /**
     * Get a list of unique appnames
     *
     * @return  array
     */
    public function getAppnames()
    {
        $query = "SELECT DISTINCT `appname`
				FROM $this->tableName
				ORDER BY `appname` ASC";

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Get a list of unique exechosts
     *
     * @return  array
     */
    public function getExechosts()
    {
        $query = "SELECT DISTINCT `exechost`
				FROM $this->tableName
				ORDER BY `exechost` ASC";

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Get a list of unique usernames
     *
     * @return  array
     */
    public function getUsernames()
    {
        $mv = new Viewperm($this->db);

        $query = "SELECT DISTINCT v.viewuser
				FROM $mv->tableName AS v
				INNER JOIN $this->tableName AS s ON v.sessnum = s.sessnum
				ORDER BY v.viewuser ASC";

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }
}
