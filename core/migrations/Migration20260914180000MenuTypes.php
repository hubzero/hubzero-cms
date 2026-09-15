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

        // A `components` menu made before the column existed - the main menu
        // migration runs first and makes one - took the column default and is
        // typed display. Whatever made it, it is the component menu.
        $this->db->setQuery(
            "UPDATE `#__menu_types` SET `type` = 'component'"
            . " WHERE `menutype` = 'components' AND `type` <> 'component'"
        );
        $this->db->query();

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

        // Not everything in there is a component route. The starter data filed
        // the front page in it, and a page about having just installed a hub -
        // both com_content articles that happen to live in the same menu. A
        // route is an item whose alias is its own component's name; anything
        // else is a page, and a page does not belong in a generated menu.
        $strays = array();

        $rows = $this->db->getQuery(true)
            ->select('id')
            ->select('alias')
            ->select('link')
            ->from('#__menu')
            ->whereEquals('menutype', 'default')
            ->fetch();

        foreach ($rows as $row) {
            $id    = (int) (is_object($row) ? $row->id : $row['id']);
            $alias = is_object($row) ? $row->alias : $row['alias'];
            $link  = (string) (is_object($row) ? $row->link : $row['link']);

            if (preg_match('/option=com_(\w+)/', $link, $m) && $m[1] === $alias) {
                continue;
            }

            $strays[$id] = $alias;
        }

        $this->db->setQuery(
            "UPDATE `#__menu` SET `menutype` = 'components' WHERE `menutype` = 'default'"
        );
        $this->db->query();

        $this->log('Renamed the "default" menu to "components" and said what it is for');

        if (!$strays) {
            return;
        }

        // They were invisible where they were, because nothing rendered that
        // menu. They stay invisible here, by saying so rather than by nobody
        // having pointed a module at them. Their level and parent do not
        // change, so neither do their paths.
        $display = $this->db->getQuery(true)
            ->select('menutype')
            ->from('#__menu_types')
            ->whereEquals('type', 'display')
            ->order('id', 'asc')
            ->value('menutype');

        if (!$display) {
            $this->log('Nowhere to move ' . count($strays) . ' pages to; left where they are', 'warning');

            return;
        }

        foreach ($strays as $id => $alias) {
            $params = $this->db->getQuery(true)
                ->select('params')
                ->from('#__menu')
                ->whereEquals('id', $id)
                ->value('params');

            $params = json_decode((string) $params, true);
            $params = is_array($params) ? $params : array();
            $params['menu_show'] = 0;

            $this->db->getQuery()
                ->update('#__menu')
                ->set(array(
                    'menutype' => $display,
                    'params'   => json_encode($params),
                ))
                ->whereEquals('id', $id)
                ->execute();

            $this->log('Moved /' . $alias . ' to ' . $display . ', hidden: it is a page, not a route');
        }
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
