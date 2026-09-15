<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Let a menu say what it is for
 *
 * A hub ships two site menus and only one of them is a menu. `mainmenu` is what
 * a visitor sees. `default` holds one flat entry per component, which is what
 * gives every component an address and an Itemid - and so its modules, its
 * template style and its place in a breadcrumb. Nothing said so. Its title was
 * "Default" and its description was empty.
 *
 * Nor was anything stopping it being displayed. It is invisible only because no
 * module happens to name it: edit the menu module, choose Default, save, and the
 * routing table is in the masthead.
 *
 * So a menu gets a type. `display` is what a visitor sees and what a menu module
 * may render. `component` is generated, one entry per component, never shown.
 * `routing` is a hub's own addresses that it does not want linked to - empty to
 * begin with, and there for anyone who wants a URL without a menu entry for it.
 *
 * Everything existing is `display` unless it is the old `default`, which is what
 * a component menu has been all along without being called one.
 **/
class Migration20260914180000MenuTypes extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__menu_types')) {
            return;
        }

        if (!$this->db->tableHasField('#__menu_types', 'type')) {
            $this->db->setQuery(
                "ALTER TABLE `#__menu_types` ADD COLUMN `type` VARCHAR(16) NOT NULL"
                . " DEFAULT 'display' COMMENT 'display, component or routing'"
            );
            $this->db->query();

            $this->log('Added a type to #__menu_types');
        }

        // The one that was a component menu without being called one
        $existing = $this->db->getQuery(true)
            ->select('id')
            ->from('#__menu_types')
            ->whereEquals('menutype', 'default')
            ->value('id');

        if (!$existing) {
            return;
        }

        // A hub that has already made its own "components" menu keeps it, and
        // the old one is only retyped rather than renamed on top of it.
        $taken = $this->db->getQuery(true)
            ->select('id')
            ->from('#__menu_types')
            ->whereEquals('menutype', 'components')
            ->value('id');

        if ($taken) {
            $this->db->setQuery(
                "UPDATE `#__menu_types` SET `type` = 'component' WHERE `id` = " . (int) $existing
            );
            $this->db->query();

            $this->log('Marked the "default" menu as a component menu; "components" was taken');

            return;
        }

        $this->db->setQuery(
            "UPDATE `#__menu_types` SET"
            . " `menutype` = 'components',"
            . " `title` = 'Components',"
            . " `description` = 'One entry per component, so every component has an address"
            . " and a page of its own. Generated; not edited here.',"
            . " `type` = 'component'"
            . " WHERE `id` = " . (int) $existing
        );
        $this->db->query();

        $this->db->setQuery(
            "UPDATE `#__menu` SET `menutype` = 'components' WHERE `menutype` = 'default'"
        );
        $this->db->query();

        $this->log('Renamed the "default" menu to "components" and said what it is for');
    }

    /**
     * Down
     **/
    public function down()
    {
        if (!$this->db->tableExists('#__menu_types')) {
            return;
        }

        $this->db->setQuery(
            "UPDATE `#__menu` SET `menutype` = 'default' WHERE `menutype` = 'components'"
        );
        $this->db->query();

        $this->db->setQuery(
            "UPDATE `#__menu_types` SET `menutype` = 'default', `title` = 'Default',"
            . " `description` = 'default' WHERE `menutype` = 'components'"
        );
        $this->db->query();

        if ($this->db->tableHasField('#__menu_types', 'type')) {
            $this->db->setQuery("ALTER TABLE `#__menu_types` DROP COLUMN `type`");
            $this->db->query();
        }
    }
}
