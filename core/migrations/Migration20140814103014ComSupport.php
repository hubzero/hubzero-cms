<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding support statuses
**/
class Migration20140814103014ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__support_statuses')) {
            // Create the table
            $schema->createTable('#__support_statuses')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->tinyInteger('open')->default(0)
                ->string('title', 250)->default('')
                ->char('alias', 250)->default('')
                ->primaryKey('id')
                ->index('idx_open', 'open')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            // Add default values
            $this->db->getQuery(true)
                ->insert('#__support_statuses')
                ->columns(['id', 'open', 'title', 'alias'])
                ->values(1, 1, 'Open', 'open')
                ->values(2, 1, 'Waiting response', 'waiting')
                ->values(3, 1, 'Waiting review', 'waitingreview')
                ->values(4, 1, 'Pending update', 'pendingupdate')
                ->execute();

            $resolutions = $this->db->getQuery(true)
                ->select('*')
                ->from('#__support_resolutions')
                ->loadObjectList();

            if ($resolutions) {
                $i = 5;
                $entries = array();
                foreach ($resolutions as $result) {
                    $entries[] = [
                        'id'    => $i,
                        'open'  => 0,
                        'title' => $result->title,
                        'alias' => $result->alias
                    ];
                    $i++;
                }

                if (count($entries)) {
                    $this->db->getQuery(true)
                        ->insertMany('#__support_statuses', $entries)
                        ->execute();
                }
            }

            // Update closed tickets
            $rows = $this->db->getQuery(true)
                ->select('*')
                ->from('#__support_statuses')
                ->where('open', '=', 0)
                ->loadObjectList();

            if ($rows) {
                foreach ($rows as $row) {
                    $this->db->getQuery(true)
                        ->update('#__support_tickets')
                        ->set(['status' => $row->id])
                        ->where('resolved', '=', $row->alias)
                        ->execute();
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__support_statuses');
    }
}
