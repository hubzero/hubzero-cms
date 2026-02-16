<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing DATETIME fields default to NULL for xmessage tables
 *
*/
class Migration20190321000000Core extends Base
{
    /**
     * List of tables and their datetime fields
     *
     * @var  array
     **/
    public static $tables = array(
        '#__extensions' => array(
            'checked_out_time'
        ),
        '#__menu' => array(
            'checked_out_time'
        )
    );

    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        foreach (self::$tables as $table => $fields) {
            foreach ($fields as $field) {
                if (
                    $schema->tableExists($table)
                    && $schema->hasColumn($table, $field)
                ) {
                    $schema->modifyColumn($table, $field)
                        ->datetime()
                        ->nullable()
                        ->default(null)
                        ->execute();

                    $this->log(sprintf('Changed `%s`.`%s` datetime default to NULL', $table, $field));

                    $this->db->getQuery(true)
                        ->update($table)
                        ->set([$field => null])
                        ->where($field, '=', '0000-00-00 00:00:00')
                        ->execute();

                    $this->log(sprintf('Cleaned up `%s`.`%s` datetime default values', $table, $field));
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

        foreach (self::$tables as $table => $fields) {
            foreach ($fields as $field) {
                if (
                    $schema->tableExists($table)
                    && $schema->hasColumn($table, $field)
                ) {
                    $schema->modifyColumn($table, $field)
                        ->datetime()
                        ->notNull()
                        ->default('0000-00-00 00:00:00')
                        ->execute();

                    $this->log(sprintf('Changed `%s`.`%s` datetime default to "0000-00-00 00:00:00"', $table, $field));
                }
            }
        }
    }
}
