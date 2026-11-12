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
class Migration20190305000000Xmessage extends Base
{
    /**
     * List of tables and their datetime fields
     *
     * @var  array
     **/
    public static $tables = [
        '#__xmessage' => [
            'created',
        ],
        '#__xmessage_recipient' => [
            'created',
            'expires',
        ],
        '#__xmessage_seen' => [
            'whenseen',
        ],
    ];

    /**
     * Up
     *
     * @return  void
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
                    $schema
                        ->modifyColumn($table, $field)
                        ->datetime()
                        ->nullable()
                        ->default(null)
                        ->execute();

                    $this->db
                        ->getQuery()
                        ->update($table)
                        ->set([$field => null])
                        ->where($field, '=', '0000-00-00 00:00:00')
                        ->execute();
                }
            }
        }
    }

    /**
     * Down
     *
     * @return  void
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
                    $schema
                        ->modifyColumn($table, $field)
                        ->datetime()
                        ->notNull()
                        ->default('0000-00-00 00:00:00')
                        ->execute();
                }
            }
        }
    }
}
