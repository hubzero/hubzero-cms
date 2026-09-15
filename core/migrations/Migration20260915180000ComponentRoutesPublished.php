<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Stop a generated address being switched off on a component's behalf
 *
 * A component's entry used to be published or not according to whether the
 * component was switched on, and a migration that disabled a component
 * unpublished its entry to match. Two records of one fact, which is two chances
 * to disagree - and they did, in one direction that nothing would have
 * reported: disable a component by migration, enable it again in the extension
 * manager, and the entry stays unpublished. The component runs, answers at its
 * address through the router's name fallback, and has no Itemid, so no modules,
 * no template style and no breadcrumb. Nothing says why.
 *
 * The menu now reads #__extensions when it loads and leaves out anything whose
 * component is switched off, so there is one record of that fact and it is the
 * one the extension manager writes. An entry's own published state goes back to
 * meaning what it means everywhere else: whether the administrator wants the
 * page.
 *
 * Which leaves the entries that were switched off on a component's behalf. They
 * are published here, because nobody chose it.
 **/
class Migration20260915180000ComponentRoutesPublished extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__menu') || !$this->db->tableHasField('#__menu_types', 'type')) {
            return;
        }

        $rows = $this->db->getQuery(true)
            ->select('m.id')
            ->select('m.path')
            ->from('#__menu', 'm')
            ->join('#__menu_types AS t', 't.menutype', 'm.menutype')
            ->whereEquals('t.type', 'component')
            ->whereEquals('m.client_id', 0)
            ->whereEquals('m.published', 0)
            ->fetch();

        $ids = array();

        foreach ($rows as $row) {
            $ids[] = (int) (is_object($row) ? $row->id : $row['id']);

            $this->log('Published the address /' . (is_object($row) ? $row->path : $row['path'])
                . '; whether it answers is now the component\'s business');
        }

        if (!$ids) {
            return;
        }

        $this->db->setQuery(
            "UPDATE `#__menu` SET `published` = 1 WHERE `id` IN (" . implode(',', $ids) . ")"
        );
        $this->db->query();
    }

    /**
     * Down
     *
     * Nothing. Switching these back off would only put back a state nobody
     * asked for.
     **/
    public function down()
    {
        // Deliberately empty
    }
}
