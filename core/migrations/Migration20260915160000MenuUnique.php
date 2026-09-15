<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Put back the unique key that installing sample data threw away
 *
 * schema.sql declares `idx_client_id_parent_id_alias_language` UNIQUE: two menu
 * items under one parent cannot share an alias. starter.sql then dropped that
 * key and added it back as a plain index, so every hub installed with sample
 * data has been running without the constraint ever since. Only a hub installed
 * without sample data kept it.
 *
 * It was dropped because the sample data broke it: two separators under Discover
 * both aliased `n`, and a main menu Support entry sharing the root with the
 * component entry it points at. Rather than make the data fit, the constraint
 * was removed - and with it the guarantee that anything else relied on.
 *
 * So: make whatever is duplicated distinct, then put the key back.
 *
 * Which one gets renamed matters. An alias item and a separator are never
 * matched against a request - the router skips them - so their alias is a label
 * and changing it moves nothing. Anything else answers at its path, and renaming
 * it would take a working address away. So the cosmetic ones go first, and a
 * group with no cosmetic member is left alone and reported rather than having a
 * URL silently changed underneath a hub.
 **/
class Migration20260915160000MenuUnique extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__menu')) {
            return;
        }

        if ($this->isUnique()) {
            return;
        }

        $blocked = 0;

        foreach ($this->duplicates() as $group) {
            // Keep one. Prefer to rename the ones nothing routes to.
            usort($group, function ($a, $b) {
                $aCosmetic = in_array($a['type'], array('alias', 'separator'), true);
                $bCosmetic = in_array($b['type'], array('alias', 'separator'), true);

                if ($aCosmetic != $bCosmetic) {
                    // The one that answers at its path stays
                    return $aCosmetic ? 1 : -1;
                }

                return $a['id'] - $b['id'];
            });

            $keep = array_shift($group);

            foreach ($group as $row) {
                if (!in_array($row['type'], array('alias', 'separator'), true)) {
                    $this->log(
                        'Left /' . $row['path'] . ' (#' . $row['id'] . ') alone: it shares an alias with #'
                        . $keep['id'] . ' and both answer at a path, so renaming either would move a'
                        . ' working address. Give one of them a different alias, then run this again.',
                        'warning'
                    );

                    $blocked++;
                    continue;
                }

                $this->rename($row);
            }
        }

        if ($blocked) {
            $this->log(
                'Leaving the menu key as it is: ' . $blocked . ' duplicate'
                . ($blocked == 1 ? '' : 's') . ' still to resolve by hand',
                'warning'
            );

            return;
        }

        $this->db->setQuery(
            "ALTER TABLE `#__menu` DROP KEY `idx_client_id_parent_id_alias_language`,"
            . " ADD UNIQUE KEY `idx_client_id_parent_id_alias_language`"
            . " (`client_id`,`parent_id`,`alias`,`language`)"
        );
        $this->db->query();

        $this->log('Two menu items under one parent can no longer share an alias');
    }

    /**
     * Down
     *
     * Back to a plain index. The renamed items keep their new aliases, because
     * nothing routed to them and putting the old ones back would only recreate
     * the collision.
     **/
    public function down()
    {
        if (!$this->db->tableExists('#__menu') || !$this->isUnique()) {
            return;
        }

        $this->db->setQuery(
            "ALTER TABLE `#__menu` DROP KEY `idx_client_id_parent_id_alias_language`,"
            . " ADD KEY `idx_client_id_parent_id_alias_language`"
            . " (`client_id`,`parent_id`,`alias`,`language`)"
        );
        $this->db->query();
    }

    /**
     * Whether the key is unique already
     *
     * @return  bool
     */
    protected function isUnique()
    {
        // SHOW INDEX names the table as an identifier, so the prefix is
        // replaced. Inside a quoted string - TABLE_NAME = '#__menu' - it is
        // not, the literal reaches MySQL as written, and this always said no.
        $this->db->setQuery(
            "SHOW INDEX FROM `#__menu`"
            . " WHERE `Key_name` = 'idx_client_id_parent_id_alias_language'"
        );

        $rows = $this->db->loadObjectList();

        if (!$rows) {
            return false;
        }

        return ((int) $rows[0]->Non_unique === 0);
    }

    /**
     * Menu items sharing a client, parent, alias and language
     *
     * @return  array  One array of rows per collision
     */
    protected function duplicates()
    {
        $this->db->setQuery(
            "SELECT `a`.`id`, `a`.`type`, `a`.`alias`, `a`.`path`, `a`.`parent_id`,"
            . " CONCAT(`a`.`client_id`, '|', `a`.`parent_id`, '|', `a`.`alias`, '|', `a`.`language`) AS `k`"
            . " FROM `#__menu` AS `a`"
            . " INNER JOIN (SELECT `client_id`, `parent_id`, `alias`, `language`"
            . " FROM `#__menu` GROUP BY `client_id`, `parent_id`, `alias`, `language`"
            . " HAVING COUNT(*) > 1) AS `d`"
            . " ON `d`.`client_id` = `a`.`client_id` AND `d`.`parent_id` = `a`.`parent_id`"
            . " AND `d`.`alias` = `a`.`alias` AND `d`.`language` = `a`.`language`"
            . " ORDER BY `a`.`id` ASC"
        );

        $groups = array();

        foreach ((array) $this->db->loadObjectList() as $row) {
            $groups[$row->k][] = array(
                'id'        => (int) $row->id,
                'type'      => (string) $row->type,
                'alias'     => (string) $row->alias,
                'path'      => (string) $row->path,
                'parent_id' => (int) $row->parent_id,
            );
        }

        return array_values($groups);
    }

    /**
     * Give an item an alias nothing else under its parent is using
     *
     * @param   array  $row  The one to rename
     * @return  void
     */
    protected function rename(array $row)
    {
        for ($n = 2; $n < 100; $n++) {
            $alias = $row['alias'] . $n;

            $this->db->setQuery(
                "SELECT COUNT(*) FROM `#__menu` WHERE `parent_id` = " . $row['parent_id']
                . " AND `alias` = " . $this->db->quote($alias)
            );

            if (!$this->db->loadResult()) {
                break;
            }
        }

        // The path is the alias with whatever came before it kept
        $path  = explode('/', $row['path']);
        array_pop($path);
        $path[] = $alias;
        $path   = implode('/', $path);

        $this->db->setQuery(
            "UPDATE `#__menu` SET `alias` = " . $this->db->quote($alias)
            . ", `path` = " . $this->db->quote($path)
            . " WHERE `id` = " . $row['id']
        );
        $this->db->query();

        $this->log(
            'Renamed ' . $row['type'] . ' #' . $row['id'] . ' from ' . $row['alias']
            . ' to ' . $alias . '; nothing routes to it'
        );
    }
}
