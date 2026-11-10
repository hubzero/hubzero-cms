<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\Lang;

/**
 * Table class for event pages
 */
class Calendar extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__events_calendars', 'id', $db);
    }

    /**
     * Check Method for saving
     *
     * @return  bool
     */
    public function check()
    {
        if (!isset($this->title) || $this->title == '') {
            $this->setError(Lang::txt('COM_EVENTS_CALENDAR_MUST_HAVE_TITLE'));
            return false;
        }
        return true;
    }

    /**
     * Find all calendars matching filters
     *
     * @param   array  $filters
     * @return  array
     */
    public function find($filters = [])
    {
        $sql  = "SELECT * FROM {$this->tableName}";
        $sql .= $this->buildQuery($filters);

        $this->db->setQuery($sql);
        return $this->db->loadObjectList();
    }

    /**
     * Get count of calendars matching filters
     *
     * @param   array  $filters
     * @return  int
     */
    public function count($filters = [])
    {
        $sql  = "SELECT COUNT(*) FROM {$this->tableName}";
        $sql .= $this->buildQuery($filters);

        $this->db->setQuery($sql);
        return $this->db->loadResult();
    }

    /**
     * Build query string for getting list or count of calendars
     *
     * @param   array   $filters
     * @return  string
     */
    private function buildQuery($filters = [])
    {
        // var to hold conditions
        $where = [];
        $sql   = '';

        // scope
        if (isset($filters['scope'])) {
            $where[] = "scope=" . $this->db->quote($filters['scope']);
        }

        // scope_id
        if (isset($filters['scope_id'])) {
            $where[] = "scope_id=" . $this->db->quote($filters['scope_id']);
        }

        // readonly
        if (isset($filters['readonly'])) {
            $where[] = "readonly=" . $this->db->quote($filters['readonly']);
        }

        // published
        if (isset($filters['published']) && is_array($filters['published'])) {
            $where[] = "published IN (" . implode(',', $filters['published']) . ")";
        }

        // if we have and conditions
        if (count($where) > 0) {
            $sql = " WHERE " . implode(" AND ", $where);
        }

        return $sql;
    }
}
