<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

use Hubzero\Database\Table;

/**
 * Table class for job application
 */
class Application extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__jobs_applications', 'id', $db);
    }

    /**
     * Get job applications
     *
     * @param   integer  $jobid  Job ID
     * @return  mixed    False if errors, Array upon success
     */
    public function getApplications($jobid)
    {
        if ($jobid === null) {
            return false;
        }

        $sql  = "SELECT a.* FROM `$this->tableName` AS a ";
        $sql .= "JOIN `#__jobs_seekers` as s ON s.uid=a.uid";
        $sql .= " WHERE  a.jid=" . $this->db->quote($jobid) . " AND s.active=1 ";
        $sql .= " ORDER BY a.applied DESC";

        $this->db->setQuery($sql);
        return $this->db->loadObjectList();
    }

    /**
     * Load a record and bind to $this
     *
     * @param   integer  $uid      User ID
     * @param   integer  $jid      Job ID
     * @param   string   $jobcode  Job code
     * @return  boolean  True upon success
     */
    public function loadApplication($uid = null, $jid = null, $jobcode = null)
    {
        if ($uid === null or ($jid === null && $jobcode === null)) {
            return false;
        }

        $query  = "SELECT A.* FROM `$this->tableName` as A ";
        $query .= $jid ? "" : " JOIN `#__jobs_openings` as J ON J.id=A.jid ";
        $query .= " WHERE A.uid=" . $this->db->quote($uid) . " ";
        if ($jid) {
            $query .= "AND A.jid=" . $this->db->quote($jid) . " ";
        } else {
            $query .= "AND J.code=" . $this->db->quote($jobcode) . " ";
        }
        $query .= " LIMIT 1";
        $this->db->setQuery($query);
        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        }
        return false;
    }
}
