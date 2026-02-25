<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for converting the timestamps in the created field to
 * standard format
**/

class Migration20160208183103ComFeedaggregator extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__feedaggregator_posts')) {
            // Grab rows first
            $query = $this->db->getQuery(true)
                ->select('*')
                ->from('#__feedaggregator_posts');
            $rows = $query->loadObjectList();

            // Convert the field
            $this->db->schema()->modifyColumn('#__feedaggregator_posts', 'created')
                ->datetime()
                ->execute();

            // Convert each timestamp into SQL date format
            foreach ($rows as $row) {
                $dt = \Hubzero\Facades\Date::of(date("F j, Y, g:i a", $row->created))->toSql();
                $this->db->getQuery(true)
                    ->update('#__feedaggregator_posts')
                    ->set(['created' => $dt])
                    ->where('id', '=', $row->id)
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__feedaggregator_posts')) {
            // Grab rows first
            $query = $this->db->getQuery(true)
                ->select('*')
                ->from('#__feedaggregator_posts');
            $rows = $query->loadObjectList();

            // Convert the field
            $this->db->schema()->modifyColumn('#__feedaggregator_posts', 'created')
                ->integer(11)
                ->execute();

            // Convert each timestamp into SQL date format
            foreach ($rows as $row) {
                $dt = \Hubzero\Facades\Date::of($row->created)->toUnix();
                $this->db->getQuery(true)
                    ->update('#__feedaggregator_posts')
                    ->set(['created' => $dt])
                    ->where('id', '=', $row->id)
                    ->execute();
            }
        }
    }
}
