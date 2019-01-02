<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

/**
 * Migration to add missing columns
 */
class Migration20190102152739ComCoursesMissingColumns extends Base
{

	static $offeringSectionsTable = '#__courses_offering_sections';
	static $pagesTable = '#__courses_pages';

	public function up()
	{
		$this->_addColumnsToOfferingSectionsTable();
		$this->_addColumnsToPagesTable();
	}

	protected function _addColumnsToOfferingSectionsTable()
	{
		$offeringSectionsTable = self::$offeringSectionsTable;

		$alterTable = "ALTER TABLE $offeringSectionsTable
			ADD COLUMN is_default tinyint(2);
			ADD COLUMN enrollment tinyint(2);";

		$this->_queryIfTableExists($offeringSectionsTable, $alterTable);
	}

	protected function _addColumnsToPagesTable()
	{
		$pagesTable = self::$pagesTable;

		$alterTable = "ALTER TABLE $pagesTable
			ADD COLUMN section_id int(11);";

		$this->_queryIfTableExists($pagesTable, $alterTable);
	}

	public function down()
	{
		$this->_removeColumnsFromOfferingSectionsTable();
		$this->_removeColumnsFromPagesTable();
	}

	protected function _removeColumnsFromOfferingSectionsTable()
	{
		$offeringSectionsTable = self::$offeringSectionsTable;

		$alterTable = "ALTER TABLE $offeringSectionsTable
			DROP COLUMN is_default,
			DROP COLUMN enrollment;";

		$this->_queryIfTableExists($offeringSectionsTable, $alterTable);
	}

	protected function _removeColumnsFromPagesTable()
	{
		$pagesTable = self::$pagesTable;

		$alterTable = "ALTER TABLE $pagesTable
			DROP COLUMN section_id;";

		$this->_queryIfTableExists($pagesTable, $alterTable);
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
