<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\User;
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;

/**
 * Course section table class
 */
class SectionDate extends Table
{
    /**
     * Contructor method for Table class
     *
     * @param   object  &$db  Database object
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__courses_offering_section_dates', 'id', $db);
    }

    /**
     * Load a record and bind it to this object
     *
     * @param   mixed    $keys   Record ID, or whatever loadForScope() takes first
     * @param   boolean  $reset  Unused; the record is always loaded afresh
     * @return  boolean  True on success
     */
    public function load($keys = null, $reset = true)
    {
        return $this->loadForScope($keys);
    }

    /**
     * Load a record and bind to $this
     *
     * @param   mixed    $oid
     * @param   string   $scope
     * @param   integer  $section_id
     * @return  boolean  True on success
     */
    public function loadForScope($oid = null, $scope = null, $section_id = null)
    {
        if ($oid === null) {
            return false;
        }
        if (is_numeric($oid) && $scope === null) {
            return parent::load($oid);
        }

        $fields = [
            'scope'    => trim($scope),
            'scope_id' => intval($oid),
        ];
        if ($section_id !== null) {
            $fields['section_id'] = intval($section_id);
        }

        return parent::load($fields);
    }

    /**
     * Override the check function to do a little input cleanup
     *
     * @return  boolean
     */
    public function check()
    {
        $this->section_id = intval($this->section_id);
        if (!$this->section_id) {
            $this->setError(Lang::txt('Please provide a section ID.'));
            return false;
        }

        $this->scope = trim($this->scope);
        if (!$this->scope) {
            $this->setError(Lang::txt('Please provide a scope.'));
            return false;
        }

        $this->scope_id = intval($this->scope_id);
        if (!$this->scope_id) {
            $this->setError(Lang::txt('Please provide a scope ID.'));
            return false;
        }

        // Blank date boxes post ''; strict SQL refuses '' for a datetime, so store NULL
        foreach (['publish_up', 'publish_down'] as $col) {
            if (isset($this->$col) && trim((string) $this->$col) === '') {
                $this->$col = null;
            }
        }

        if (!$this->id) {
            $this->created    = Date::toSql();
            $this->created_by = User::get('id');

            // Make sure the record doesn't already exist
            $query  = "SELECT id FROM $this->tableName WHERE scope=" . $this->db->quote($this->scope)
                . " AND scope_id=" . $this->db->quote($this->scope_id);
            $query .= " AND section_id=" . $this->db->quote($this->section_id);
            $query .= " LIMIT 1";

            $this->db->setQuery($query);
            if ($id = $this->db->loadResult()) {
                $this->id = $id;
            }
        }

        return true;
    }

    /**
     * Build query method
     *
     * @param   array   $filters
     * @return  string  SQL
     */
    private function buildQuery($filters = [])
    {
        $query  = " FROM $this->tableName AS sd";

        $where = [];

        if (isset($filters['section_id']) && $filters['section_id'] >= 0) {
            $where[] = "sd.section_id=" . $this->db->quote(intval($filters['section_id']));
        }

        if (isset($filters['scope']) && $filters['scope']) {
            $where[] = "sd.scope=" . $this->db->quote($filters['scope']);
        }

        if (isset($filters['scope_id']) && $filters['scope_id'] > 0) {
            $where[] = "sd.scope_id=" . $this->db->quote(intval($filters['scope_id']));
        }

        if (count($where) > 0) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        return $query;
    }

    /**
     * Get a count of entries
     *
     * @param   array    $filters
     * @return  string|null
     */
    public function count($filters = [])
    {
        $query  = "SELECT COUNT(sd.id)";
        $query .= $this->buildQuery($filters);

        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Get a list of entries
     *
     * @param   array  $filters
     * @return  array
     */
    public function find($filters = [])
    {
        $query  = "SELECT sd.*";
        $query .= $this->buildQuery($filters);

        if (!isset($filters['sort']) || $filters['sort'] == '') {
            $filters['sort'] = 'sd.publish_up';
        }
        if (!isset($filters['sort_Dir']) || !in_array(strtoupper($filters['sort_Dir']), ['ASC', 'DESC'])) {
            $filters['sort_Dir'] = 'ASC';
        }

        $query .= " ORDER BY "
            . (preg_replace('/[^a-zA-Z0-9_,. ]/', '', (string) $filters['sort']) ?: '1')
            . " "
            . (strtoupper((string) $filters['sort_Dir']) === 'ASC' ? 'ASC' : 'DESC');

        if (isset($filters['limit']) && $filters['limit'] != 0) {
            if (!isset($filters['start'])) {
                $filters['start'] = 0;
            }
            $query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
        }

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Delete all records by section ID
     *
     * @param   integer  $section_id
     * @return  boolean
     */
    public function deleteBySection($section_id)
    {
        $query  = "DELETE FROM $this->tableName WHERE `section_id`=" . $this->db->quote($section_id);

        $this->db->setQuery($query);
        if (!$this->db->query()) {
            return false;
        }
        return true;
    }
}
