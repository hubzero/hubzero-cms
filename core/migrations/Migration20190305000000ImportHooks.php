<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing DATETIME fields default to NULL for import_hooks table
 *
*/
class Migration20190305000000ImportHooks extends Base
{
    /**
     * List of tables and their datetime fields
     *
     * @var  array
     **/
    public static $tables = array(
        '#__import_hooks' => array(
            'created'
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

                    $this->db->getQuery(true)
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
                }
            }
        }
    }
}
