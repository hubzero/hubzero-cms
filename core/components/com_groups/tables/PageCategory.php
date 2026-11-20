<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\Lang;

/**
 * Groups Pages Category table
 *
 * @property  mixed  $gidNumber
 * @property  mixed  $title
 */
class PageCategory extends Table
{
    /**
     * Constructor
     *
     * @param   \Hubzero\Database\Driver  &$db  Database
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
     * @param   array<mixed>  $filters
     * @return  array<int, \stdClass>
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
     * @param   array<mixed>  $filters
     * @return  array<int, \stdClass>
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
     * @param   array<mixed>   $filters
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
     * @param   object|mixed  $group
     * @return  array<mixed>
     */
    public function getCategories($group)
    {
        $categories = [];

        // make sure we have a valid group
        if (!$group instanceof \Hubzero\User\Group || $group->get('gidNumber') == '') {
            return $categories;
        }

        $sql = "SELECT * FROM {$this->tableName} WHERE gidNumber=" . $this->db->quote($group->get('gidNumber'));
        $this->db->setQuery($sql);
        return $this->db->loadObjectList();
    }
}
