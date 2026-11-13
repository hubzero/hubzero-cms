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
 * Table for host types
 */
class Hosttype extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('hosttype', 'name', $db);
    }

    /**
     * Validate data
     *
     * @return  boolean  False if invalid data, true on success
     */
    public function check()
    {
        if (!$this->name) {
            $this->setError(Lang::txt('No name provided'));
            return false;
        }

        if (!$this->description) {
            $this->setError(Lang::txt('No description provided.'));
            return false;
        }

        if (!$this->value) {
            $this->setError(Lang::txt('No value provided.'));
            return false;
        }

        return true;
    }

    /**
     * Inserts a new row if id is zero or updates an existing row in the database table
     *
     * Can be overloaded/supplemented by the child class
     *
     * @param   boolean  $insert
     * @param   boolean  $updateNulls  If false, null object variables are not updated
     * @return  mixed    null|string null if successful otherwise returns and error message
     */
    public function store($insert = null, $updateNulls = false)
    {
        $k = $this->tableKey;

        if ($insert) {
            $this->db->insertObject($this->tableName, $this, $this->tableKey);
        } else {
            if ($this->$k) {
                $this->db->updateObject($this->tableName, $this, $this->tableKey, $updateNulls);
            } else {
                $this->db->insertObject($this->tableName, $this, $this->tableKey);
            }
        }

        return true;
    }

    /**
     * Updates a row in a table based on an object's properties.
     *
     * @param   string   $key      The name of the primary key.
     * @param   boolean  $nulls    True to update null fields or false to ignore them.
     * @return  boolean  True on success.
     */
    public function update($key = null, $nulls = false)
    {
        // Initialise variables.
        $fields = [];
        $where = '';

        // Create the base update statement.
        $statement = 'UPDATE ' . $this->db->quoteName($this->tableName) . ' SET %s WHERE %s';

        if (!$key) {
            $key = $this->{$this->tableKey};
        }

        $where = $this->db->quoteName($this->tableKey) . '=' . $this->db->quote($key);

        // Iterate over the object variables to build the query fields/value pairs.
        foreach ($this->getProperties() as $k => $v) {
            // Only process scalars that are not internal fields.
            if (is_array($v) or is_object($v) or $k[0] == '_') {
                continue;
            }

            // Prepare and sanitize the fields and values for the database query.
            if ($v === null) {
                // If the value is null and we want to update nulls then set it.
                if ($nulls) {
                    $val = 'NULL';
                } else {
                    // If the value is null and we do not want to update nulls then ignore this field.
                    continue;
                }
            } else {
                // The field is not null so we prep it for update.
                $val = $this->db->quote($v);
            }

            // Add the field to be updated.
            $fields[] = $this->db->quoteName($k) . '=' . $val;
        }

        // We don't have any fields to update.
        if (empty($fields)) {
            return true;
        }

        // Set the query and execute the update.
        $this->db->setQuery(sprintf($statement, implode(",", $fields), $where));
        return $this->db->execute();
    }

    /**
     * Default delete method
     *
     * can be overloaded/supplemented by the child class
     *
     * @param   integer  $oid
     * @return  bool     if successful otherwise returns and error message
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

        if (isset($filters['search']) && $filters['search'] != '') {
            $where[] = "(LOWER(c.name) LIKE "
                . $this->db->quote('%' . strtolower($filters['search']) . '%')
                . " OR LOWER(c.description) LIKE "
                . $this->db->quote('%' . strtolower($filters['search']) . '%') . ")";
        }

        $query = "FROM $this->tableName AS c";
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
        $query  = "SELECT c.* " . $this->buildQuery($filters);

        if (!isset($filters['sort']) || !$filters['sort']) {
            $filters['sort'] = 'name';
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
            $query .= ' LIMIT ' . $filters['start'] . ',' . $filters['limit'];
        }

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }
}
