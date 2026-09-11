<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Stop advertising a Store that isn't there
 *
 * The starter data set shipped a published Store alias in the main menu that
 * pointed at a menu item which is itself unpublished, for a component the same
 * data set disables. mod_menu renders an alias on the strength of the alias's
 * own state and never looks at what it resolves to, so every page of every hub
 * built from that data offered a Store link in its primary navigation, and the
 * link answered 404: with com_storefront disabled the router cannot build a
 * route for the target, so the Itemid never resolves to a URL.
 *
 * starter.sql now ships the alias unpublished. This is for the hubs that were
 * installed before that.
 *
 * A hub that actually sells something fails the test below twice over - its
 * storefront component is enabled and its Store menu item is published - so
 * this only reaches the hubs where the link has never worked and nobody has
 * touched it.
 **/
class Migration20260911120200ComStorefront extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!($aliases = $this->deadStoreAliases(1))) {
            return;
        }

        $this->setPublished($aliases, 0);

        $this->log(
            'Store links that went nowhere are no longer in the menu: '
            . count($aliases) . ' unpublished.'
        );
    }

    /**
     * Down
     **/
    public function down()
    {
        if (!($aliases = $this->deadStoreAliases(0))) {
            return;
        }

        $this->setPublished($aliases, 1);
    }

    /**
     * Menu aliases in the given state that point at an unpublished storefront
     * item behind a disabled component
     *
     * All three have to hold together. Any one of them failing means somebody
     * has arranged this menu deliberately, and it is theirs.
     *
     * @param   integer  $published  The state to look for on the alias itself
     * @return  array    Menu item ids
     **/
    protected function deadStoreAliases($published)
    {
        if (!$this->db->tableExists('#__menu') || !$this->db->tableExists('#__extensions')) {
            return array();
        }

        $this->db->setQuery(
            "SELECT COUNT(*) FROM `#__extensions`"
            . " WHERE `type` = 'component' AND `element` = 'com_storefront' AND `enabled` = 1"
        );

        // The store works, or could. Leave the menu alone.
        if ($this->db->loadResult()) {
            return array();
        }

        $this->db->setQuery(
            "SELECT `id`, `params` FROM `#__menu`"
            . " WHERE `type` = 'alias' AND `published` = " . (int) $published
        );

        $ids = array();

        foreach ((array) $this->db->loadObjectList() as $alias) {
            $params = json_decode((string) $alias->params, true);

            if (!isset($params['aliasoptions']) || !(int) $params['aliasoptions']) {
                continue;
            }

            $this->db->setQuery(
                "SELECT `link`, `published` FROM `#__menu`"
                . " WHERE `id` = " . (int) $params['aliasoptions']
            );

            $target = $this->db->loadObject();

            if (!$target || $target->published == 1) {
                continue;
            }

            if (strpos($target->link, 'option=com_storefront') !== false) {
                $ids[] = (int) $alias->id;
            }
        }

        return $ids;
    }

    /**
     * Set the published state of the given menu items
     *
     * @param   array    $ids    Menu item ids
     * @param   integer  $state  The state to set
     * @return  void
     **/
    protected function setPublished($ids, $state)
    {
        $this->db->setQuery(
            "UPDATE `#__menu` SET `published` = " . (int) $state
            . " WHERE `id` IN (" . implode(',', array_map('intval', $ids)) . ")"
        );
        $this->db->query();
    }
}
