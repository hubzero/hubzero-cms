<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

$componentPath = Component::path('com_courses');

require_once "$componentPath/helpers/queryAddColumnStatement.php";
require_once "$componentPath/helpers/queryDropColumnStatement.php";

use Components\Courses\Helpers\QueryAddColumnStatement;
use Components\Courses\Helpers\QueryDropColumnStatement;

/**
 * Migration to add missing columns
 */
class Migration20190102152739ComCoursesMissingColumns extends Base
{

	static $assetsTable = '#__courses_assets';
	static $assetsColumns = [
		 ['name' => 'grade_weight', 'type' => 'varchar(255)'],
		 ['name' => 'graded', 'type' => 'tinyint(2)'],
		 ['name' => 'subtype', 'type' => 'varchar(255)', 'restriction' => 'NOT NULL ', 'default' => "'file'"],
	];
	static $offeringSectionsTable = '#__courses_offering_sections';
	static $offeringSectionsColumns = [
		 ['name' => 'enrollment', 'type' => 'tinyint(2)'],
		 ['name' => 'is_default', 'type' => 'tinyint(2)', 'restriction' => 'NOT NULL ', 'default' => 0],
	];
	static $pagesTable = '#__courses_pages';
	static $pagesColumns = [
		 ['name' => 'section_id', 'type' => 'int(11)'],
	];

	public function up()
	{
		$assetsTable = self::$assetsTable;
		$assetsTableQuery = $this->_generateSafeAddColumns($assetsTable, self::$assetsColumns);
		$this->_queryIfTableExists($assetsTable, $assetsTableQuery);

		$offeringSectionsTable = self::$offeringSectionsTable;
		$offeringSectionsTableQuery = $this->_generateSafeAddColumns($offeringSectionsTable, self::$offeringSectionsColumns);
		$this->_queryIfTableExists($offeringSectionsTable, $offeringSectionsTableQuery);

		$pagesTable = self::$pagesTable;
		$pagesTableQuery = $this->_generateSafeAddColumns($pagesTable, self::$pagesColumns);
		$this->_queryIfTableExists($pagesTable, $pagesTableQuery);
	}

	public function down()
	{
		$assetsTable = self::$assetsTable;
		$assetsTableQuery = $this->_generateSafeDropColumns($assetsTable, self::$assetsColumns);
		$this->_queryIfTableExists($assetsTable, $assetsTableQuery);

		$offeringSectionsTable = self::$offeringSectionsTable;
		$offeringSectionsTableQuery = $this->_generateSafeDropColumns($offeringSectionsTable, self::$offeringSectionsColumns);
		$this->_queryIfTableExists($offeringSectionsTable, $offeringSectionsTableQuery);

		$pagesTable = self::$pagesTable;
		$pagesTableQuery = $this->_generateSafeDropColumns($pagesTable, self::$pagesColumns);
		$this->_queryIfTableExists($pagesTable, $pagesTableQuery);
	}

	protected function _generateSafeAddColumns($table, $columns)
	{
		$query = $this->_generateSafeAlterTableColumnOperation(
			$table, $columns, '_safeAddColumn'
		);

		return $query;
	}

	protected function _safeAddColumn($table, $columnData)
	{
		$columnName = $columnData['name'];
		$addColumnStatement = '';

		if (!$this->db->tableHasField($table, $columnName))
		{
			$addColumnStatement = (new QueryAddColumnStatement($columnData))
				->toString();
		}

		return $addColumnStatement;
	}

	protected function _generateSafeDropColumns($table, $columns)
	{
		$query = $this->_generateSafeAlterTableColumnOperation(
			$table, $columns, '_safeDropColumn'
		);

		return $query;
	}

	protected function _safeDropColumn($table, $columnData)
	{
		$columnName = $columnData['name'];
		$dropColumnStatement = '';

		if ($this->db->tableHasField($table, $columnName))
		{
			$dropColumnStatement = (new QueryDropColumnStatement($columnData))
				->toString();
		}

		return $dropColumnStatement;
	}

	protected function _generateSafeAlterTableColumnOperation($table, $columns, $functionName)
	{
		$query = "ALTER TABLE $table ";

		foreach ($columns as $columnData)
		{
			$query .= $this->$functionName($table, $columnData) . ',';
		}

		$query = rtrim($query, ',') . ';';

		return $query;
	}

	protected function _queryIfTableExists($tableName, $query)
	{
		if ($this->db->tableExists($tableName))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

}
