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
class Page extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__xgroups_pages', 'id', $db);
    }

    /**
     * Validate data
     *
     * @return  boolean
     */
    public function check()
    {
        // need group id
        if ($this->get('gidNumber') == null) {
            $this->setError(Lang::txt('Must provide group id.'));
            return false;
        }

        // need page title
        if ($this->get('title') == null) {
            $this->setError(Lang::txt('Must provide page title.'));
            return false;
        }

        // need page alias
        if ($this->get('alias') == null) {
            $this->setError(Lang::txt('Must provide page alias.'));
            return false;
        }

        return true;
    }

    /**
     * Find all pages matching filters
     *
     * @param   array  $filters
     * @return  array
     */
    public function find($filters = [])
    {
        $sql  = "SELECT * FROM {$this->tableName}";
        $sql .= $this->buildQuery($filters);

        $this->db->setQuery($sql);

        if (isset($filters['returnas']) && $filters['returnas'] == 'array') {
            return $this->db->loadAssocList();
        } else {
            return $this->db->loadObjectList();
        }
    }

    /**
     * Get count of pages matching filters
     *
     * @param   array   $filters
     * @return  string|null
     */
    public function count($filters = [])
    {
        $sql  = "SELECT COUNT(*) FROM {$this->tableName}";
        $sql .= $this->buildQuery($filters);

        $this->db->setQuery($sql);
        return $this->db->loadResult();
    }

    /**
     * Build query string for getting list or count of pages
     *
     * @param   array   $filters
     * @return  string
     */
    private function buildQuery($filters = [])
    {
        // var to hold conditions
        $where = [];
        $sql   = '';

        // published
        if (isset($filters['gidNumber'])) {
            $where[] = "gidNumber=" . $this->db->quote($filters['gidNumber']);
        }

        // published
        if (isset($filters['state']) && is_array($filters['state'])) {
            $where[] = "state IN (" . implode(',', $filters['state']) . ")";
        }

        // category
        if (isset($filters['category'])) {
            $where[] = "category=" . $this->db->quote($filters['category']);
        }

        // home
        if (isset($filters['home'])) {
            $where[] = "home=" . $this->db->quote($filters['home']);
        }

        // parent
        if (isset($filters['depth'])) {
            $where[] = "depth=" . $this->db->quote($filters['depth']);
        }

        // left
        if (isset($filters['left'])) {
            $where[] = "lft > " . $this->db->quote($filters['left']);
        }

        // right
        if (isset($filters['right'])) {
            $where[] = "rgt < " . $this->db->quote($filters['right']);
        }

        // if we have and conditions
        if (count($where) > 0) {
            $sql = " WHERE " . implode(" AND ", $where);
        }

        if (isset($filters['orderby'])) {
            $sql .= " ORDER BY " . (preg_replace('/[^a-zA-Z0-9_,. ]/', '', (string) $filters['orderby']) ?: '1');
        }

        return $sql;
    }
}
