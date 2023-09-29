<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

/**
 * Migration to correct contents of jos_courses_asset_views.viewed_by column 
 * (column should contain values jos_user.id not jos_courses_members.id)
 */
class Migration20230929000000ComCoursesFixAssetViews extends Base
{

	static $targetTable = '#__courses_asset_views';
	static $sourceTable = '#__courses_members';
	static $column = [
		['name' => 'viewed_by_member', 'type' => 'int(11)', 'default' => 'NULL']
	];

	public function up()
	{
		$targetTable = self::$targetTable;
		$sourceTable = self::$sourceTable;
		$column = self::$column;

		// add viewed_by_member column to asset views table:
		$query = $this->_generateSafeAddColumns($targetTable, $column);
		$this->_queryIfTableExists($targetTable, $query);

		if ($this->db->tableHasField($targetTable, $column['name']))
		{
			// update viewed_by_member column with current contents of viewed_by:
			$query = "UPDATE $targetTable, 
				(SELECT id, viewed_by FROM $targetTable) AS sub
        			SET $targetTable.viewed_by_member = sub.viewed_by
        			WHERE $targetTable.id = sub.id";
			$this->_queryIfTableExists($targetTable, $query);

			// update viewed_by column with contents of jos_courses_members.user_id:
			$query = "UPDATE $targetTable t
        			SET viewed_by = (select sub.user_id from $sourceTable sub
            			WHERE t.viewed_by_member = sub.id)";
			$this->_queryIfTableExists($targetTable, $query);
		}
		
	}

	public function down()
	{
		$targetTable = self::$targetTable;
		$column = self::$column;

		// revert the fix:
		// update viewed_by column with current contents of viewed_by_member:
		$query = "UPDATE $targetTable, 
			(SELECT id, viewed_by_member FROM $targetTable) AS sub
        		SET $targetTable.viewed_by = sub.viewed_by_member
        		WHERE $targetTable.id = sub.id";
		$this->_queryIfTableExists($targetTable, $query);

		// drop viewed_by_member column from table:
		$query = $this->_generateSafeDropColumns($targetTable, $column);
		$this->_queryIfTableExists($targetTable, $query);
	}
}
