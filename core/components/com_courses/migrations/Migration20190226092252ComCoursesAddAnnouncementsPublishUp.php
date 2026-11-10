<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Migrations;

use Hubzero\Content\Migration\Base;
use Components\Courses\Helpers\QueryAddColumnStatement;
use Components\Courses\Helpers\QueryDropColumnStatement;

/**
 * Migration to add missing columns
 */
class Migration20190226092252ComCoursesAddAnnouncementsPublishUp extends Base
{
    public static $announcementsTable = '#__courses_announcements';

    public static $announcementsColumns = [
        ['name' => 'publish_down', 'type' => 'timestamp', 'default' => "'0000-00-00 00:00:00'"],
        ['name' => 'publish_up', 'type' => 'timestamp', 'default' => "'0000-00-00 00:00:00'"],
        ['name' => 'sticky', 'type' => 'tinyint(2)', 'default' => '0'],
    ];

    public function up()
    {
        $announcementsTable = self::$announcementsTable;
        $query = $this->generateSafeAddColumns($announcementsTable, self::$announcementsColumns);
        $this->queryIfTableExists($announcementsTable, $query);
    }

    public function down()
    {
        $announcementsTable = self::$announcementsTable;
        $query = $this->generateSafeDropColumns($announcementsTable, self::$announcementsColumns);
        $this->queryIfTableExists($announcementsTable, $query);
    }

    protected function generateSafeAddColumns($table, $columns)
    {
        $query = $this->generateSafeAlterTableColumnOperation(
            $table,
            $columns,
            'safeAddColumn',
        );

        return $query;
    }

    protected function safeAddColumn($table, $columnData)
    {
        $columnName = $columnData['name'];
        $addColumnStatement = '';

        if (!$this->db->tableHasField($table, $columnName)) {
            $addColumnStatement = (new QueryAddColumnStatement($columnData))
                ->toString();
        }

        return $addColumnStatement;
    }

    protected function generateSafeDropColumns($table, $columns)
    {
        $query = $this->generateSafeAlterTableColumnOperation(
            $table,
            $columns,
            'safeDropColumn',
        );

        return $query;
    }

    protected function safeDropColumn($table, $columnData)
    {
        $columnName = $columnData['name'];
        $dropColumnStatement = '';

        if ($this->db->tableHasField($table, $columnName)) {
            $dropColumnStatement = (new QueryDropColumnStatement($columnData))
                ->toString();
        }

        return $dropColumnStatement;
    }

    protected function generateSafeAlterTableColumnOperation($table, $columns, $functionName)
    {
        $query = "ALTER TABLE $table ";

        foreach ($columns as $columnData) {
            $query .= $this->$functionName($table, $columnData) . ',';
        }

        $query = rtrim($query, ',') . ';';

        return $query;
    }

    protected function queryIfTableExists($tableName, $query)
    {
        if ($this->db->tableExists($tableName)) {
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
