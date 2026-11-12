<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Tables;

use Hubzero\Database\Table;

/**
 * Groups Module table
 */
class ModuleMenu extends Table
{
    /**
     * Constructor
     *
     * @param   object  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__xgroups_modules_menu', 'id', $db);
    }

    /**
     * Get pages of module menu
     *
     * @return  array|null
     */
    public function getMenu($filters = [])
    {
        if (!isset($filters['moduleid'])) {
            return null;
        }

        $sql = "SELECT * FROM {$this->tableName} as m WHERE m.`moduleid`=" . $this->db->quote($filters['moduleid']);
        $this->db->setQuery($sql);
        return $this->db->loadObjectList();
    }

    /**
     * Create menus matching module ID
     *
     * @param   integer  $moduleid
     * @param   array    $pages
     * @return  boolean
     */
    public function createMenus($moduleid, $pages = [])
    {
        // create array of values to insert
        $values = [];
        foreach ($pages as $pageid) {
            $values[] = '(' . $this->db->quote($moduleid) . ',' . $this->db->quote($pageid) . ')';
        }

        // make sure we have at least one menu item
        if (count($values) < 1) {
            $this->setError('Module must have at least one menu assignment.');
            return false;
        }

        // add menu items for each page
        $sql = "INSERT INTO {$this->tableName}(`moduleid`,`pageid`) VALUES " . implode(',', $values);
        $this->db->setQuery($sql);
        if (!$this->db->query()) {
            $this->setError($this->db->getError());
            return false;
        }

        // everything went smoothly
        return true;
    }

    /**
     * Delete menus matching module ID
     *
     * @param   integer  $moduleid  Module ID
     * @return  boolean
     */
    public function deleteMenus($moduleid)
    {
        // make sure we have a module id
        if (!$moduleid) {
            $this->setError('You must supply a module ID.');
            return false;
        }

        // delete any menu items matching module id
        $sql = "DELETE FROM {$this->tableName} WHERE `moduleid`=" . $this->db->quote($moduleid);
        $this->db->setQuery($sql);
        if (!$this->db->query()) {
            $this->setError($this->db->getError());
            return false;
        }

        // everything went smoothly
        return true;
    }
}
