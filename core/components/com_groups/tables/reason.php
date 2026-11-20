<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\Lang;

/**
 * Table class for group membership reason
 *
 * @property  mixed  $date
 * @property  mixed  $gidNumber
 * @property  mixed  $reason
 * @property  mixed  $uidNumber
 */
class Reason extends Table
{
    /**
     * Constructor
     *
     * @param   \Hubzero\Database\Driver  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__xgroups_reasons', 'id', $db);
    }

    /**
     * Load a record based on group ID and user ID and bind to $this
     *
     * @param   integer|null  $uid  User ID
     * @param   integer|null  $gid  Group ID
     * @return  boolean  True on success
     */
    public function loadReason($uid, $gid)
    {
        if ($uid === null || $gid === null) {
            return false;
        }
        $query = "SELECT * FROM $this->tableName WHERE uidNumber=" . $this->db->quote($uid)
            . " AND gidNumber=" . $this->db->quote($gid) . " LIMIT 1";
        $this->db->setQuery($query);
        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        } else {
            return false;
        }
    }

    /**
     * Delete an entry based on group ID and user ID
     *
     * @param   integer|null  $uid  User ID
     * @param   integer|null  $gid  Group ID
     * @return  boolean  True on success
     */
    public function deleteReason($uid, $gid)
    {
        if ($uid === null || $gid === null) {
            return false;
        }
        $query = "DELETE FROM $this->tableName WHERE uidNumber=" . $this->db->quote($uid)
            . " AND gidNumber=" . $this->db->quote($gid);
        $this->db->setQuery($query);
        $this->db->query();
        return true;
    }

    /**
     * Validate data
     *
     * @return  boolean  True if data is valid
     */
    public function check()
    {
        if (trim($this->gidNumber) == '') {
            $this->setError(Lang::txt('GROUPS_REASON_MUST_HAVE_GROUPID'));
            return false;
        }

        if (trim($this->uidNumber) == '') {
            $this->setError(Lang::txt('GROUPS_REASON_MUST_HAVE_USERNAME'));
            return false;
        }

        return true;
    }
}
