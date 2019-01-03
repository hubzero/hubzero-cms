<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

/**
 * Migration to add missing columns
 */
class Migration20190102152739ComCoursesMissingColumns extends Base
{

	static $assetsTable = '#__courses_assets';
	static $offeringSectionsTable = '#__courses_offering_sections';
	static $pagesTable = '#__courses_pages';

	public function up()
	{
		$assetsTable = self::$assetsTable;
		$assetsTableQuery = "ALTER TABLE $assetsTable
			ADD COLUMN grade_weight varchar(255),
			ADD COLUMN graded tinyint(2),
			ADD subtype varchar(255) NOT NULL DEFAULT 'file';";
		$this->_queryIfTableExists($assetsTable, $assetsTableQuery);

		$offeringSectionsTable = self::$offeringSectionsTable;
		$offeringSectionsTableQuery = "ALTER TABLE $offeringSectionsTable
			ADD COLUMN enrollment tinyint(2),
			ADD is_default tinyint(2) NOT NULL DEFAULT 0;";
		$this->_queryIfTableExists($offeringSectionsTable, $offeringSectionsTableQuery);

		$pagesTable = self::$pagesTable;
		$pagesTableQuery = "ALTER TABLE $pagesTable
			ADD COLUMN section_id int(11);";
		$this->_queryIfTableExists($pagesTable, $pagesTableQuery);
	}

	public function down()
	{
		$assetsTable = self::$assetsTable;
		$assetsTableQuery  = "ALTER TABLE $assetsTable
			DROP COLUMN grade_weight,
			DROP COLUMN graded,
			DROP COLUMN subtype;";
		$this->_queryIfTableExists($assetsTable, $assetsTableQuery);

		$offeringSectionsTable = self::$offeringSectionsTable;
		$offeringSectionsTableQuery = "ALTER TABLE $offeringSectionsTable
			DROP COLUMN enrollment,
			DROP COLUMN is_default;";
		$this->_queryIfTableExists($offeringSectionsTable, $offeringSectionsTableQuery);

		$pagesTable = self::$pagesTable;
		$pagesTableQuery = "ALTER TABLE $pagesTable
			DROP COLUMN section_id;";
		$this->_queryIfTableExists($pagesTable, $pagesTableQuery);
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
