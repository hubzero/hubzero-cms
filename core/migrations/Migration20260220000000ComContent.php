<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add missing fieldset legend to home page resources search form
 */
class Migration20260220000000ComContent extends Base
{
    /**
     * Up
     */
    public function up()
    {
        $row = $this->db->getQuery(true)
            ->select(['id', 'introtext'])
            ->from('#__content')
            ->where('id', '=', 1)
            ->first();

        if (!$row || strpos($row->introtext, '<fieldset>') === false) {
            return;
        }

        // Must contain the sample.sql search field
        if (strpos($row->introtext, 'label for="rsearchword"') === false) {
            return;
        }

        // Already has the legend
        if (strpos($row->introtext, 'Search resources</legend>') !== false) {
            return;
        }

        $updated = preg_replace(
            '/<fieldset>(\s*)<label\s+for="rsearchword">/',
            '<fieldset>$1<legend class="sr-only">Search resources</legend>$1<label for="rsearchword">',
            $row->introtext
        );

        if ($updated !== $row->introtext) {
            $this->db->getQuery(true)
                ->update('#__content')
                ->set(['introtext' => $updated])
                ->where('id', '=', $row->id)
                ->execute();
        }
    }

    /**
     * Down
     */
    public function down()
    {
        $row = $this->db->getQuery(true)
            ->select(['id', 'introtext'])
            ->from('#__content')
            ->where('id', '=', 1)
            ->first();

        if (!$row) {
            return;
        }

        $updated = preg_replace(
            '/<legend class="sr-only">Search resources<\/legend>\s*/',
            '',
            $row->introtext
        );

        if ($updated !== $row->introtext) {
            $this->db->getQuery(true)
                ->update('#__content')
                ->set(['introtext' => $updated])
                ->where('id', '=', $row->id)
                ->execute();
        }
    }
}
