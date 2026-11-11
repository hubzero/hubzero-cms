<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

use Hubzero\Database\Table;

/**
 * Table class for job category
 */
class Category extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__jobs_categories', 'id', $db);
    }

    /**
     * Get all records
     *
     * @param   string   $sortby     Field to sort by
     * @param   string   $sortdir    Sort direction (ASC/DESC)
     * @param   integer  $getobject  Return records as objects?
     * @return  array
     */
    public function getCats($sortby = 'ordernum', $sortdir = 'ASC', $getobject = 0)
    {
        $cats = [];

        $query  = $getobject ? "SELECT * " : "SELECT id, category ";
        $query .= "FROM `$this->tableName` ORDER BY "
            . (preg_replace('/[^a-zA-Z0-9_,. ]/', '', (string) $sortby) ?: '1')
            . " "
            . (strtoupper((string) $sortdir) === 'DESC' ? 'DESC' : 'ASC')
            . "";
        $this->db->setQuery($query);
        $result = $this->db->loadObjectList();
        if ($getobject) {
            return $result;
        }

        if ($result) {
            foreach ($result as $r) {
                $cats[$r->id] = $r->category;
            }
        }

        return $cats;
    }

    /**
     * Get a category
     *
     * @param   integer  $id       Category ID
     * @param   string   $default  Default value if no record found
     * @return  mixed    False if errors, String upon success
     */
    public function getCat($id = null, $default = 'Unspecified')
    {
        if ($id === null) {
            return false;
        }
        if ($id == 0) {
            return $default;
        }

        $query  = "SELECT category FROM `$this->tableName` WHERE id=" . $this->db->quote($id);
        $this->db->setQuery($query);
        return $this->db->loadResult();
    }

    /**
     * Update the ordering of records
     *
     * @param   integer  $id        Category ID
     * @param   integer  $ordernum  ORder number to make it
     * @return  boolean  True upon success
     */
    public function updateOrder($id = null, $ordernum = 1)
    {
        if ($id == null or !intval($ordernum)) {
            return false;
        }

        $query  = "UPDATE `$this->tableName` SET ordernum=" . $this->db->quote($ordernum);
        $query .= " WHERE id=" . $this->db->quote($id);
        $this->db->setQuery($query);
        if (!$this->db->query()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }
        return true;
    }
}
