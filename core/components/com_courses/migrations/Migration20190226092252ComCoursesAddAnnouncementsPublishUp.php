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
class Migration20190226092252ComCoursesAddAnnouncementsPublishUp extends Base
{

	static $announcementsTable = '#__courses_announcements';
	static $announcementsColumns = [
		['name' => 'publish_down', 'type' => 'timestamp', 'default' => "'0000-00-00 00:00:00'"],
		['name' => 'publish_up', 'type' => 'timestamp', 'default' => "'0000-00-00 00:00:00'"],
		['name' => 'sticky', 'type' => 'tinyint(2)', 'default' => '0']
	];

	public function up()
	{
		$announcementsTable = self::$announcementsTable;
		$query = $this->_generateSafeAddColumns($announcementsTable, self::$announcementsColumns);
		$this->_queryIfTableExists($announcementsTable, $query);
	}

	public function down()
	{
		$announcementsTable = self::$announcementsTable;
		$query = $this->_generateSafeDropColumns($announcementsTable, self::$announcementsColumns);
		$this->_queryIfTableExists($announcementsTable, $query);
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
