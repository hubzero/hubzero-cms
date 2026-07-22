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
 * Migration to support ordering of course offerings.
 *
 * Adds a manual `ordering` column to course offerings (so they can be sorted
 * into a custom order) and backfills a publish date for already-published
 * offerings that never had one recorded, so the public course page can order
 * its offerings by publish date instead of by an undefined sort key.
 */
class Migration20260630000000ComCoursesOfferingOrdering extends Base
{
	static $table = '#__courses_offerings';

	public function up()
	{
		$table = self::$table;

		if (!$this->db->tableHasField($table, 'ordering'))
		{
			$this->_queryIfTableExists(
				$table,
				"ALTER TABLE `$table` ADD `ordering` INT(11) NOT NULL DEFAULT 0"
			);
		}

		// Data fix: published offerings were never given a publish_up date,
		// leaving the public course page unable to order them. Seed it from the
		// created date as the best available approximation; admins can correct
		// individual dates afterward.
		$this->_queryIfTableExists(
			$table,
			"UPDATE `$table`
			 SET `publish_up` = `created`
			 WHERE `state` = 1
			 AND (`publish_up` IS NULL OR `publish_up` = '0000-00-00 00:00:00')
			 AND `created` IS NOT NULL
			 AND `created` <> '0000-00-00 00:00:00'"
		);
	}

	public function down()
	{
		$table = self::$table;

		if ($this->db->tableHasField($table, 'ordering'))
		{
			$this->_queryIfTableExists(
				$table,
				"ALTER TABLE `$table` DROP `ordering`"
			);
		}
	}
}
