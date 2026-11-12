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
 * Groups Pages Category table
 */
class PageCategory extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__xgroups_pages_categories', 'id', $db);
    }

    /**
     * Check method overload
     *
     * @return  bool
     */
    public function check()
    {
        // make sure we have a title
        if (!$this->gidNumber || $this->gidNumber == "") {
            $this->setError(Lang::txt('Category Must Contain Group ID Number'));
            return false;
        }

        // make sure we have a title
        if (!$this->title || $this->title == "") {
            $this->setError(Lang::txt('Category Must Contain Title'));
            return false;
        }

        return true;
    }

    /**
     * Find all records matching filters
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
     * Get count of records matching filters
     *
     * @param   array  $filters
     * @return  int
     */
    public function count($filters = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->tableName}";
        $sql .= $this->buildQuery($filters);

        $this->db->setQuery($sql);
        return $this->db->loadObjectList();
    }

    /**
     * Build query string for getting list or count of records
     *
     * @param   array   $filters
     * @return  string
     */
    private function buildQuery($filters = [])
    {
        //vars
        $sql   = '';
        $where = [];

        // check for gidNumber
        if (isset($filters['gidNumber'])) {
            $where[] = 'gidNumber=' . $this->db->quote($filters['gidNumber']);
        }

        // did we have any conditions
        if (count($where) > 0) {
            $sql = ' WHERE ' . implode(' AND', $where);
        }

        // check for gidNumber
        if (isset($filters['orderby'])) {
            $sql .= ' ORDER BY ' . (preg_replace('/[^a-zA-Z0-9_,. ]/', '', (string) $filters['orderby']) ?: '1');
        }

        return $sql;
    }

    /**
     * Get categories for a group
     *
     * @param   object  $group
     * @return  array
     */
    public function getCategories($group)
    {
        $categories = [];

        // make sure we have a valid group
        if (!is_object($group) || $group->get('gidNumber') == '') {
            return $categories;
        }

        $sql = "SELECT * FROM {$this->tableName} WHERE gidNumber=" . $this->db->quote($group->get('gidNumber'));
        $this->db->setQuery($sql);
        return $this->db->loadObjectList();
    }
}
