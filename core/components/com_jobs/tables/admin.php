<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

use Hubzero\Database\Table;

/**
 * Table class for job admins
 */
class Admin extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__jobs_admins', 'id', $db);
    }

    /**
     * Check if a user is an admin for a job
     *
     * @param   integer  $uid  User ID
     * @param   integer  $jid  Job ID
     * @return  boolean  True if admin
     */
    public function isAdmin($uid, $jid)
    {
        if ($uid === null or $jid === null) {
            return false;
        }

        $query  = "SELECT id ";
        $query .= "FROM `$this->tableName` ";
        $query .= "WHERE uid = " . $this->db->quote($uid) . " AND jid = " . $this->db->quote($jid);
        $this->db->setQuery($query);
        if ($this->db->loadResult()) {
            return true;
        }
        return false;
    }

    /**
     * Get a list of administrators
     *
     * @param   integer  $jid  Job ID
     * @return  array|false
     */
    public function getAdmins($jid)
    {
        if ($jid === null) {
            return false;
        }

        $admins = [];

        $query  = "SELECT uid ";
        $query .= "FROM `$this->tableName` ";
        $query .= "WHERE jid = " . $this->db->quote($jid);
        $this->db->setQuery($query);
        $result = $this->db->loadObjectList();
        if ($result) {
            foreach ($result as $r) {
                $admins[] = $r->uid;
            }
        }

        return $admins;
    }
}
