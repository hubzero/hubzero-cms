<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing DATETIME fields default to NULL for collections_votes table
 **/
class Migration20190305000000ComCollections extends Base
{
    /**
     * List of tables and their datetime fields
     *
     * @var  array
     **/
    public static $tables = array(
        '#__collections_votes' => array(
            'created'
        )
    );

    /**
     * Up
     **/
    public function up()
    {
        foreach (self::$tables as $table => $fields) {
            foreach ($fields as $field) {
                if (
                    $this->db->tableExists($table)
                    && $this->db->tableHasField($table, $field)
                ) {
                    $query = "ALTER TABLE `$table` CHANGE `$field` `$field` DATETIME  NULL  DEFAULT NULL";

                    $this->db->setQuery($query);
                    $this->db->query();

                    $query = "UPDATE `$table` SET `$field`=NULL WHERE `$field`='0000-00-00 00:00:00'";

                    $this->db->setQuery($query);
                    $this->db->query();
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        foreach (self::$tables as $table => $fields) {
            foreach ($fields as $field) {
                if (
                    $this->db->tableExists($table)
                    && $this->db->tableHasField($table, $field)
                ) {
                    $query = "ALTER TABLE `$table` CHANGE `$field` `$field` "
                        . "DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00'";

                    $this->db->setQuery($query);
                    $this->db->query();
                }
            }
        }
    }
}
