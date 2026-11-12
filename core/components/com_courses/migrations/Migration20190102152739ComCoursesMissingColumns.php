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
class Migration20190102152739ComCoursesMissingColumns extends Base
{
    public static $assetsTable = '#__courses_assets';

    public static $assetsColumns = [
        ['name' => 'grade_weight', 'type' => 'varchar(255)'],
        ['name' => 'graded', 'type' => 'tinyint(2)'],
        ['name' => 'subtype', 'type' => 'varchar(255)', 'restriction' => 'NOT NULL ', 'default' => "'file'"],
    ];

    public static $offeringSectionsTable = '#__courses_offering_sections';

    public static $offeringSectionsColumns = [
        ['name' => 'enrollment', 'type' => 'tinyint(2)'],
        ['name' => 'is_default', 'type' => 'tinyint(2)', 'restriction' => 'NOT NULL ', 'default' => 0],
    ];

    public static $pagesTable = '#__courses_pages';

    public static $pagesColumns = [
        ['name' => 'section_id', 'type' => 'int(11)'],
    ];

    /**
     * Up
     *
     * @return  void
     */
    public function up()
    {
        $assetsTable = self::$assetsTable;
        $assetsTableQuery = $this->generateSafeAddColumns($assetsTable, self::$assetsColumns);
        $this->queryIfTableExists($assetsTable, $assetsTableQuery);

        $offeringSectionsTable = self::$offeringSectionsTable;
        $offeringSectionsTableQuery = $this->generateSafeAddColumns(
            $offeringSectionsTable,
            self::$offeringSectionsColumns,
        );
        $this->queryIfTableExists($offeringSectionsTable, $offeringSectionsTableQuery);

        $pagesTable = self::$pagesTable;
        $pagesTableQuery = $this->generateSafeAddColumns($pagesTable, self::$pagesColumns);
        $this->queryIfTableExists($pagesTable, $pagesTableQuery);
    }

    /**
     * Down
     *
     * @return  void
     */
    public function down()
    {
        $assetsTable = self::$assetsTable;
        $assetsTableQuery = $this->generateSafeDropColumns($assetsTable, self::$assetsColumns);
        $this->queryIfTableExists($assetsTable, $assetsTableQuery);

        $offeringSectionsTable = self::$offeringSectionsTable;
        $offeringSectionsTableQuery = $this->generateSafeDropColumns(
            $offeringSectionsTable,
            self::$offeringSectionsColumns,
        );
        $this->queryIfTableExists($offeringSectionsTable, $offeringSectionsTableQuery);

        $pagesTable = self::$pagesTable;
        $pagesTableQuery = $this->generateSafeDropColumns($pagesTable, self::$pagesColumns);
        $this->queryIfTableExists($pagesTable, $pagesTableQuery);
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
            $addColumnStatement = new QueryAddColumnStatement($columnData)
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
            $dropColumnStatement = new QueryDropColumnStatement($columnData)
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
