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
 * Course Units table class
 */
class Unit extends Table
{
    /**
     * Contructor method for Table class
     *
     * @param  database object
     * @return void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__courses_units', 'id', $db);
    }

    /**
     * Populate the current object with a database record if found
     * Accepts either an alias or an ID
     *
     * @param      mixed $oid Unique ID or alias of object to retrieve
     * @return     boolean True on success
     */
    public function load($oid = null, $offering_id = null)
    {
        if (empty($oid)) {
            return false;
        }

        if (is_numeric($oid)) {
            return parent::load($oid);
        }

        $sql  = "SELECT * FROM $this->tableName WHERE `alias`=" . $this->db->quote($oid);
        if ($offering_id) {
            $sql .= " AND `offering_id`=" . $this->db->quote($offering_id);
        }
        $sql .= " AND `state`!=2 LIMIT 1";
        $this->db->setQuery($sql);
        if ($result = $this->db->loadAssoc()) {
            return $this->bind($result);
        } else {
            $this->setError($this->db->getErrorMsg());
            return false;
        }
    }

    /**
     * Override the check function to do a little input cleanup
     *
     * @return return true
     */
    public function check()
    {
        $this->offering_id = intval($this->offering_id);
        if (!$this->offering_id) {
            $this->setError(Lang::txt('Please provide a course offering ID.'));
            return false;
        }

        $this->title = trim($this->title);
        if (!$this->title) {
            $this->setError(Lang::txt('Please provide a title.'));
            return false;
        }

        if (!$this->alias) {
            $this->alias = strtolower($this->title);
        }
        $this->alias = preg_replace("/[^a-zA-Z0-9\-_]/", '', $this->alias);
        $this->makeAliasUnique();

        if (!$this->id) {
            $this->created    = Date::toSql();
            $this->created_by = User::get('id');

            $this->state = ($this->state) ? $this->state : 1;

            if (!$this->ordering) {
                $this->ordering = $this->getHighestOrdering($this->offering_id);
                $this->ordering = (!$this->ordering) ? 1 : $this->ordering;
            }
        }

        return true;
    }

    /**
     * Build query method
     *
     * @param  array $filters
     * @return $query database query
     */
    private function buildQuery($filters = [])
    {
        $query = " FROM $this->tableName AS cu
					LEFT JOIN #__courses_offering_section_dates AS sd ON sd.scope='unit' AND sd.scope_id=cu.id";

        if (isset($filters['section_id']) && $filters['section_id']) {
            $query .= " AND sd.section_id=" . $this->db->quote($filters['section_id']);
        }

        $where = [];

        // Note the deliberate lack of a truthiness test: an offering_id of 0
        // has to match no units, not every unit in the table.
        if (isset($filters['offering_id'])) {
            $where[] = "cu.offering_id=" . $this->db->quote($filters['offering_id']);
        }
        if (isset($filters['state']) && $filters['state'] >= 0) {
            $where[] = "cu.state=" . $this->db->quote($filters['state']);
        }
        if (isset($filters['search']) && $filters['search']) {
            $where[] = "(LOWER(cu.alias) LIKE " . $this->db->quote('%' . strtolower($filters['search']) . '%') . "
					OR LOWER(cu.title) LIKE " . $this->db->quote('%' . strtolower($filters['search']) . '%') . ")";
        }

        if (count($where) > 0) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        return $query;
    }

    /**
     * Get a count of course offering units
     *
     * @param  array $filters
     * @return object Return course units
     */
    public function count($filters = [])
    {
        $query  = "SELECT COUNT(cu.id)";
        $query .= $this->buildQuery($filters);

        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Get an object list of course offering units
     *
     * @param  array $filters
     * @return object Return course units
     */
    public function find($filters = [])
    {
        $query  = "SELECT DISTINCT cu.*, sd.publish_up, sd.publish_down, sd.section_id";
        $query .= $this->buildQuery($filters);

        $query .= " ORDER BY cu.ordering";

        if (isset($filters['limit']) && (int) $filters['limit'] > 0) {
            $query .= " LIMIT "
                . (int) (isset($filters['start']) ? $filters['start'] : 0)
                . ","
                . (int) $filters['limit'];
        }

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Get the last page in the ordering
     *
     * @param      string  $offering_id
     * @return     integer
     */
    public function getHighestOrdering($offering_id)
    {
        $sql = "SELECT MAX(ordering)+1 FROM $this->tableName WHERE offering_id="
            . $this->db->quote(intval($offering_id));
        $this->db->setQuery($sql);
        return $this->db->loadResult();
    }

    /**
     * Return a unique alias based on given alias
     *
     * @return     void
     */
    private function makeAliasUnique()
    {
        $sql = "SELECT alias from $this->tableName WHERE `offering_id`="
            . $this->db->quote(intval($this->offering_id))
            . " AND `id`!="
            . $this->db->quote(intval($this->id));
        $this->db->setQuery($sql);
        $result = $this->db->loadColumn();

        $original_alias = $this->alias;

        if ($result) {
            for ($i = 1; in_array($this->alias, $result); $i++) {
                $this->alias = $original_alias . $i;
            }
        }
    }
}
