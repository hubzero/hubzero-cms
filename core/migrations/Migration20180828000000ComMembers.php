<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing duplicate extended profile entries
 *
*/
class Migration20180828000000ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__user_profiles')) {
            $rows = $this->db->getQuery(true)
                ->select(['user_id', 'profile_key', 'profile_value'])
                ->select('count(*)', 'no_of_records')
                ->select('group_concat(id)', 'duplicates')
                ->from('#__user_profiles')
                ->group(['user_id', 'profile_key', 'profile_value'])
                ->having('count(*) > 1')
                ->order('user_id', 'ASC')
                ->order('profile_key', 'ASC')
                ->loadObjectList();

            $delete = array();

            foreach ($rows as $i => $row) {
                $dupes = explode(',', $row->duplicates);

                if (empty($dupes) || count($dupes) < 2) {
                    unset($rows[$i]);
                    continue;
                }

                $dupes = array_map('intval', $dupes);

                // Sort lowest to highest
                sort($dupes);

                // Discard the first (original/oldest record)
                $first = array_shift($dupes);

                // Add the other entries to the list ot delete
                foreach ($dupes as $dupe) {
                    $delete[] = $dupe;
                }

                unset($rows[$i]);
            }

            if (!empty($delete)) {
                $this->db->getQuery(true)
                    ->delete('#__user_profiles')
                    ->whereIn('id', $delete)
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        // Nothing here
    }
}
