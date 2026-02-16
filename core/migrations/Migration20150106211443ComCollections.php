<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for making default collections private.
 *
 * This is a follow-up migration to an earlier one that did
 * not take into account records where access was set to "1"
 * by Joomla.
 *
 */
class Migration20150106211443ComCollections extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__collections')) {
            $this->db->getQuery(true)
                ->update('#__collections')
                ->set(['access' => 4])
                ->where('is_default', '=', 1)
                ->where('access', '=', 1)
                ->where('created', '<', '2014-06-30 00:00:00')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__collections')) {
            $this->db->getQuery(true)
                ->update('#__collections')
                ->set(['access' => 0])
                ->where('is_default', '=', 1)
                ->where('access', '=', 4)
                ->where('created', '<', '2014-06-30 00:00:00')
                ->execute();
        }
    }
}
