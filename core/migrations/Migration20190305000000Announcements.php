<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing DATETIME fields default to NULL for announcements table
  *
**/
class Migration20190305000000Announcements extends Base
{
    /**
     * List of tables and their datetime fields
     *
     * @var  array
     **/
    public static $tables = array(
        '#__announcements' => array(
            'created',
            'publish_up',
            'publish_down'
        )
    );

    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        foreach (self::$tables as $tableName => $fields) {
            if (!$schema->tableExists($tableName)) {
                continue;
            }

            foreach ($fields as $field) {
                if ($schema->hasColumn($tableName, $field)) {
                    $schema->modifyColumn($tableName, $field)
                        ->datetime()
                        ->nullable()
                        ->default(null)
                        ->execute();

                    $this->db->getQuery(true)
                        ->update($tableName)
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

        foreach (self::$tables as $tableName => $fields) {
            if (!$schema->tableExists($tableName)) {
                continue;
            }

            foreach ($fields as $field) {
                if ($schema->hasColumn($tableName, $field)) {
                    $schema->modifyColumn($tableName, $field)
                        ->datetime()
                        ->notNull()
                        ->default('0000-00-00 00:00:00')
                        ->execute();
                }
            }
        }
    }
}
