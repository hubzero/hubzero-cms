<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\Lang;

/**
 * Table class for job resumes
 */
class Resume extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__jobs_resumes', 'id', $db);
    }

    /**
     * Validate data
     *
     * @return  boolean  True if data is valid
     */
    public function check()
    {
        if (intval($this->uid) == 0) {
            $this->setError(Lang::txt('ERROR_MISSING_UID'));
            return false;
        }

        if (trim($this->filename) == '') {
            $this->setError(Lang::txt('ERROR_MISSING_FILENAME'));
            return false;
        }

        return true;
    }

    /**
     * Load a record and bind to $this
     *
     * @param   integer  $name  Parameter description (if any) ...
     * @return  boolean  True upon success
     */
    public function loadResume($name = null)
    {
        if ($name !== null) {
            $this->tableKey = 'uid';
        }
        $k = $this->tableKey;
        if ($name !== null) {
            $this->$k = $name;
        }
        $name = $this->$k;
        if ($name === null) {
            return false;
        }

        $query = "SELECT * FROM `$this->tableName` WHERE $this->tableKey="
            . $this->db->quote($name) . " AND main='1' LIMIT 1";
        $this->db->setQuery($query);
        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        }
        return false;
    }

    /**
     * Delete a record
     *
     * @param   integer  $id  Resume ID
     * @return  boolean  False if errors, True upon success
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function deleteResume($id = null)
    {
        if ($id === null) {
            $id == $this->id;
        }
        if ($id === null) {
            return false;
        }

        $query  = "DELETE FROM `$this->tableName` WHERE id=" . $this->db->quote($id);
        $this->db->setQuery($query);
        if (!$this->db->query()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }
        return true;
    }

    /**
     * Get resume files
     *
     * @param   string   $pile   shortlisted or applied
     * @param   integer  $uid    User ID
     * @param   integer  $admin  Admin access?
     * @return  array
     */
    public function getResumeFiles($pile = 'all', $uid = 0, $admin = 0)
    {
        $query  = "SELECT DISTINCT r.uid, r.filename FROM `$this->tableName` AS r ";
        $query .= "JOIN `#__jobs_seekers` AS s ON s.uid=r.uid ";
        if ($pile == 'shortlisted' && $uid) {
            $query .= " JOIN `#__jobs_shortlist` AS W ON W.seeker=s.uid AND W.emp=" . $this->db->quote($uid)
                . " AND s.uid != " . $this->db->quote($uid) . " AND s.uid=r.uid AND W.category='resume' ";
        }
        $uid = $admin ? 1 : $uid;
        if ($pile == 'applied' && $uid) {
            $query .= " LEFT JOIN `#__jobs_openings` AS J ON J.employerid=" . $this->db->quote($uid)
                . " JOIN #__jobs_applications AS A ON A.jid=J.id AND A.uid=s.uid AND A.status=1 ";
        }
        $query .= "WHERE s.active=1 AND r.main=1 ";

        $files = [];

        $this->db->setQuery($query);
        $result = $this->db->loadObjectList();

        if ($result) {
            foreach ($result as $r) {
                $files[$r->uid] = $r->filename;
            }
        }

        return array_unique($files);
    }
}
