<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Let a menu item declare the address it answers at
 *
 * A menu item's address is derived from where it sits in the menu: the parent's
 * path, a slash, the alias. Nest Resources under a Discover heading and it stops
 * answering at /resources and starts answering at /discover/resources.
 *
 * That is why a hub's main menu is full of alias items. The real entry lives in
 * a flat menu nobody displays, at the address it has always had, and the visible
 * menu holds a second item that points at it. Two rows, one page, and the tree
 * is a lie about the URLs.
 *
 * So `route` - empty for everything that exists, which is the point. Nothing on
 * any hub changes address when this runs. An item that fills it in answers there
 * instead, wherever it happens to sit in the menu, and what is nested under it
 * follows unless it declares its own.
 **/
class Migration20260915120000MenuRoute extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__menu')) {
            return;
        }

        if ($this->db->tableHasField('#__menu', 'route')) {
            return;
        }

        $this->db->setQuery(
            "ALTER TABLE `#__menu` ADD COLUMN `route` VARCHAR(1024) DEFAULT NULL"
            . " COMMENT 'An address of its own, so a page can sit anywhere in the menu"
            . " and still answer where it always did.' AFTER `path`"
        );
        $this->db->query();

        $this->log('Added a route to #__menu. Nothing has changed address.');
    }

    /**
     * Down
     **/
    public function down()
    {
        if (!$this->db->tableExists('#__menu')) {
            return;
        }

        if (!$this->db->tableHasField('#__menu', 'route')) {
            return;
        }

        // Anything that declared a route is about to go back to being addressed
        // by where it sits, so say which ones and what they will become.
        $declared = $this->db->getQuery(true)
            ->select('path')
            ->from('#__menu')
            ->whereIsNotNull('route')
            ->where('route', '!=', '')
            ->fetch();

        foreach ($declared as $row) {
            $this->log(
                'The address /' . (is_object($row) ? $row->path : $row['path'])
                . ' was declared and will now follow the menu again',
                'warning'
            );
        }

        $this->db->setQuery("ALTER TABLE `#__menu` DROP COLUMN `route`");
        $this->db->query();
    }
}
