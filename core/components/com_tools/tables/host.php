<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\Lang;

/**
 * Middleware host table class
 */
class Host extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('host', 'hostname', $db);
    }

    /**
     * Validate data
     *
     * @return  boolean  False if invalid data, true on success
     */
    public function check()
    {
        if (!$this->hostname) {
            $this->setError(Lang::txt('No hostname provided'));
            return false;
        }
        $this->hostname = preg_replace("/[^A-Za-z0-9-.]/", '', $this->hostname);

        if (!$this->status) {
            $this->setError(Lang::txt('No status provided.'));
            return false;
        }

        return true;
    }

    /**
     * Inserts a new row if id is zero or updates an existing row in the database table
     *
     * @param   boolean  $insert       If true, forces an insert
     * @param   boolean  $kv           If false, null object variables are not updated
     * @param   boolean  $updateNulls  If false, null object variables are not updated
     * @return  mixed    null|string null if successful otherwise returns and error message
     */
    public function store($insert = null, $kv = null, $updateNulls = false)
    {
        $k = $this->tableKey;

        if ($insert) {
            $this->db->insertObject($this->tableName, $this, $this->tableKey);
        } else {
            if ($this->$k) {
                //$ret = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
                $fmtsql = "UPDATE $this->tableName SET %s WHERE %s";
                $where = $this->tableKey . '=' . ($kv ? $this->db->quote($kv) : $this->db->quote($this->$k));
                $tmp = [];
                foreach ($this->getProperties() as $ky => $v) {
                    if (is_array($v) or is_object($v) or $ky[0] == '_') { // internal or NA field
                        continue;
                    }
                    if ($v === null) {
                        continue;
                    } else {
                        //$val = $this->_db->isQuoted($ky) ? $this->_db->quote($v) : (int) $v;
                        $val = $this->db->quote($v);
                    }
                    $tmp[] = $this->db->quoteName($ky) . '=' . $val;
                }
                $this->db->setQuery(sprintf($fmtsql, implode(',', $tmp), $where));
                $this->db->query();
            } else {
                $this->db->insertObject($this->tableName, $this, $this->tableKey);
            }
        }
        return true;
    }

    /**
     * Default delete method
     *
     * @param   integer  $oid  Record ID
     * @return  boolean  True if successful otherwise returns and error message
     */
    public function delete($oid = null)
    {
        $k = $this->tableKey;
        if ($oid) {
            $this->$k = $oid;
        }

        $query = 'DELETE FROM ' . $this->db->quoteName($this->tableName)
                . ' WHERE ' . $this->tableKey . ' = ' . $this->db->quote($this->$k);
        $this->db->setQuery($query);

        if ($this->db->query()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Construct an SQL statement based on the array of filters passed
     *
     * @param   array   $filters  Filters to build SQL from
     * @return  string  SQL
     */
    private function buildQuery($filters = [])
    {
        $where = [];

        if (isset($filters['status']) && $filters['status'] != '') {
            $where[] = "c.`status`=" . $this->db->quote($filters['status']);
        }
        if (isset($filters['portbase']) && $filters['portbase'] != '') {
            $where[] = "c.`portbase`=" . $this->db->quote($filters['portbase']);
        }
        if (isset($filters['uses']) && $filters['uses'] != '') {
            $where[] = "c.`uses`=" . $this->db->quote($filters['uses']);
        }
        if (isset($filters['provisions']) && $filters['provisions'] != '') {
            $where[] = "c.`provisions`=" . $this->db->quote($filters['provisions']);
        }
        if (isset($filters['search']) && $filters['search'] != '') {
            $where[] = "(LOWER(c.hostname) LIKE " . $this->db->quote('%' . strtolower($filters['search']) . '%') . ")";
        }

        $query = "FROM $this->tableName AS c";
        $query .= " LEFT JOIN zones AS v ON v.id = c.zone_id";
        if (isset($filters['hosttype']) && $filters['hosttype']) {
            $query .= " JOIN hosttype AS t ON c.provisions & t.value != 0";
            $where[] = "t.name = " . $this->db->quote($filters['hosttype']);
        }
        if (count($where) > 0) {
            $query .= " WHERE ";
            $query .= implode(" AND ", $where);
        }

        return $query;
    }

    /**
     * Get a record count
     *
     * @param   array   $filters  Filters to build SQL from
     * @return  string|null
     */
    public function getCount($filters = [])
    {
        $filters['limit'] = 0;

        $query = "SELECT COUNT(*) " . $this->buildQuery($filters);

        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Get a list of records
     *
     * @param   array  $filters  Filters to build SQL from
     * @return  array
     */
    public function getRecords($filters = [])
    {
        $query  = "SELECT c.*, v.zone " . $this->buildQuery($filters);

        if (!isset($filters['sort']) || !$filters['sort']) {
            $filters['sort'] = 'hostname';
        }
        if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) {
            $filters['sort_Dir'] = 'ASC';
        }
        if (!in_array(strtoupper($filters['sort_Dir']), ['ASC', 'DESC'])) {
            $filters['sort_Dir'] = 'ASC';
        }
        $query .= " ORDER BY "
            . (preg_replace('/[^a-zA-Z0-9_,. ]/', '', (string) $filters['sort']) ?: '1')
            . " "
            . (strtoupper((string) $filters['sort_Dir']) === 'ASC' ? 'ASC' : 'DESC');

        if (isset($filters['limit']) && $filters['limit'] != 0) {
            $query .= ' LIMIT ' . (int) $filters['start'] . ',' . (int) $filters['limit'];
        }

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }
}
