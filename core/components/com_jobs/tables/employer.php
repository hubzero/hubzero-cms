<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\Date;

/**
 * Table class for job employer
 */
class Employer extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__jobs_employers', 'id', $db);
    }

    /**
     * Check if a user is an employer
     *
     * @param   string   $uid
     * @param   integer  $admin
     * @return  boolean
     */
    public function isEmployer($uid, $admin = 0)
    {
        if ($uid === null) {
            return false;
        }

        $now = Date::toSql();
        $query  = "SELECT e.id FROM `$this->tableName` AS e  ";
        if (!$admin) {
            $query .= "JOIN `#__users_points_subscriptions` AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
            $query .= "WHERE e.uid = " . $this->db->quote($uid) . " AND s.status=1";
            $query .= " AND s.expires > " . $this->db->quote($now) . " ";
        } else {
            $query .= "WHERE e.uid = 1";
        }
        $this->db->setQuery($query);
        if ($this->db->loadResult()) {
            return true;
        }
        return false;
    }

    /**
     * Load a record and bind to $this
     *
     * @param   integer  $uid  User ID
     * @return  boolean  True upon success
     */
    public function loadEmployer($uid = null)
    {
        if ($uid === null) {
            return false;
        }

        $this->db->setQuery("SELECT * FROM `$this->tableName` WHERE uid=" . $this->db->quote($uid));
        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        }
        return false;
    }

    /**
     * Get an employer
     *
     * @param   integer $ uid               User ID
     * @param   string   $subscriptionCode  Subscription code
     * @return  mixed    False if errors, Array upon success
     */
    public function getEmployer($uid = null, $subscriptionCode = null)
    {
        if ($uid === null or $subscriptionCode === null) {
            return false;
        }
        $query  = "SELECT e.* ";
        $query .= "FROM `$this->tableName` AS e ";
        if ($subscriptionCode == 'admin') {
            $query .= "WHERE e.uid = 1";
        } elseif ($subscriptionCode) {
            $query .= "JOIN `#__users_points_subscriptions` AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
            $query .= "WHERE s.code=" . $this->db->quote($subscriptionCode);
        } elseif ($uid) {
            $query .= "WHERE e.uid = " . $this->db->quote($uid);
        }
        $this->db->setQuery($query);
        $result = $this->db->loadObjectList();
        if ($result) {
            return $result[0];
        }
        return false;
    }
}
