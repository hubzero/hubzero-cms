<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tables;

use Hubzero\Database\Table;

/**
 * Table class for middleware view permissions
 */
class Viewperm extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('viewperm', 'sessnum', $db);
    }

    /**
     * Load database rows
     *
     * @param   integer  $sess      Session number
     * @param   string   $username  User to load for
     * @return  array
     */
    public function loadViewperm($sess = null, $username = null)
    {
        if ($sess == null) {
            $sess = $this->sessnum;
        }
        if ($sess === null) {
            return false;
        }
        $query = "SELECT * FROM $this->tableName WHERE sessnum=" . $this->db->quote($sess);
        if ($username) {
            $query .=  " AND viewuser=" . $this->db->quote($username);
        }
        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Update View perm
     *
     * @return  void
     */
    public function updateViewPerm()
    {
        if (!isset($this->sessnum) || $this->sessnum === null || $this->sessnum == '') {
            return false;
        }

        if (!isset($this->viewuser) || $this->viewuser === null || $this->viewuser == '') {
            return false;
        }

        $sql = "UPDATE `viewperm` SET "
            . "`viewtoken`=" . $this->db->quote($this->viewtoken) . ", "
            . "`geometry`=" . $this->db->quote($this->geometry) . ", "
            . "`fwhost`=" . $this->db->quote($this->fwhost) . ", "
            . "`fwport`=" . $this->db->quote($this->fwport) . ", "
            . "`vncpass`=" . $this->db->quote($this->vncpass) . ", "
            . "`readonly`=" . $this->db->quote($this->readonly)
            . " WHERE `sessnum`=" . $this->db->quote($this->sessnum)
            . " AND `viewuser`=" . $this->db->quote($this->viewuser);
        $this->db->setQuery($sql);
        $this->db->query();
    }

    /**
     * Delete a record
     *
     * @param   integer  $sess      Session number
     * @param   string   $username  User to delete for
     * @return  boolean  False if errors, True if success
     */
    public function deleteViewperm($sess = null, $username = null)
    {
        if ($sess == null) {
            $sess = $this->sessnum;
        }
        if ($sess === null) {
            return false;
        }
        $query = "DELETE FROM $this->tableName WHERE sessnum=" . $this->db->quote($sess);
        if ($username) {
            $query .=  " AND viewuser=" . $this->db->quote($username);
        }
        $this->db->setQuery($query);
        if (!$this->db->query()) {
            $this->setError(get_class($this) . '::delete failed - ' . $this->db->getErrorMsg());
            return false;
        }
        return true;
    }

    /**
     * Update a record
     *
     * @param   boolean  $updateNulls  Update null values?
     * @return  boolean  False if errors, True if success
     */
    public function update($updateNulls = false)
    {
        $ret = $this->db->updateObject($this->tableName, $this, $this->tableKey, $updateNulls);

        if (!$ret) {
            $this->setError(get_class($this) . '::update failed - ' . $this->db->getErrorMsg());
            return false;
        }
        return true;
    }

    /**
     * Insert a new record
     *
     * @return  boolean  False if errors, True if success
     */
    public function insert()
    {
        $ret = $this->db->insertObject($this->tableName, $this, $this->tableKey);

        if (!$ret) {
            $this->setError(get_class($this) . '::insert failed - ' . $this->db->getErrorMsg());
            return false;
        }
        return true;
    }
}
