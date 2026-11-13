<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\Lang;

/**
 * Table class for group page
 */
class PageVersion extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__xgroups_pages_versions', 'id', $db);
    }

    /**
     * Validate data
     *
     * @return  boolean
     */
    public function check()
    {
        /*
        // need page ID
        if ($this->get('pageid') == null || $this->get('pageid') == 0)
        {
            $this->setError(\Lang::txt('Page version must have a page ID.'));
            return false;
        }

        // need page version number
        if ($this->get('version') == null)
        {
            $this->setError(\Lang::txt('Page version must have a version number.'));
            return false;
        }
        */

        // need page content
        if ($this->get('content') == null || $this->get('content') == '') {
            $this->setError(Lang::txt('Page version must contain content.'));
            return false;
        }

        return true;
    }

    /**
     * Get a list of records
     *
     * @param   array  $filters
     * @return  array
     */
    public function find($filters = [])
    {
        $sql = "SELECT * FROM {$this->tableName}";
        $sql .= $this->buildQuery($filters);

        $this->db->setQuery($sql);
        return $this->db->loadObjectList();
    }

    /**
     * Get a count of records
     *
     * @param   array  $filters
     * @return  string|null
     */
    public function count($filters = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->tableName}";
        $sql .= $this->buildQuery($filters);

        $this->db->setQuery($sql);
        return $this->db->loadResult();
    }

    /**
     * Build search query
     *
     * @param   array  $filters
     * @return  string
     */
    private function buildQuery($filters = [])
    {
        $where = [];
        $sql   = '';

        if (isset($filters['pageid']) && is_numeric($filters['pageid'])) {
            $where[] = 'pageid=' . $this->db->quote($filters['pageid']);
        }

        if (isset($filters['version']) && is_numeric($filters['version'])) {
            $where[] = 'version=' . $this->db->quote($filters['version']);
        }

        if (count($where) > 0) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        if (isset($filters['orderby'])) {
            $sql .= " ORDER BY " . (preg_replace('/[^a-zA-Z0-9_,. ]/', '', (string) $filters['orderby']) ?: '1');
        }

        if (isset($filters['limit'])) {
            $sql .= " LIMIT " . (int) $filters['limit'];
        }

        if (isset($filters['offset'])) {
            $sql .= " OFFSET " . $filters['offset'];
        }

        return $sql;
    }
}
