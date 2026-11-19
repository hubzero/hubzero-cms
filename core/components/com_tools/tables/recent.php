<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tables;

use Hubzero\Database\Table;

/**
 * Table class for recent tools
 *
 * @property  mixed  $created
 * @property  mixed  $id
 * @property  mixed  $tool
 * @property  mixed  $uid
 */
class Recent extends Table
{
    /**
     * Constructor
     *
     * @param   \Hubzero\Database\Driver  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__recent_tools', 'id', $db);
    }

    /**
     * Get a list of recently used tools
     *
     * @param   integer|null  $uid  User ID
     * @return  array<mixed>|false
     */
    public function getRecords($uid = null)
    {
        if ($uid == null) {
            $uid = $this->uid;
        }
        if ($uid === null) {
            return false;
        }
        $query = "SELECT * FROM $this->tableName WHERE uid=" . $this->db->quote($uid)
            . " ORDER BY created DESC";
        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }
}
