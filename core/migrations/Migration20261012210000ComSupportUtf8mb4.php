<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to store 4-byte characters (emoji) in support tickets
 * and comments. With utf8 (3-byte) columns the text was cut off at the
 * first such character.
 **/
class Migration20261012210000ComSupportUtf8mb4 extends Base
{
    /**
     * Tables to convert
     *
     * @var  array
     **/
    protected $tables = array(
        '#__support_tickets',
        '#__support_comments'
    );

    /**
     * Up
     **/
    public function up()
    {
        foreach ($this->tables as $table) {
            if ($this->db->tableExists($table)) {
                $query = "ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
                $this->db->setQuery($query);
                $this->db->query();
            }
        }
    }

    /**
     * Down
     *
     * Converting back would truncate any 4-byte characters stored since, so
     * leave the tables as they are
     **/
    public function down()
    {
    }
}
