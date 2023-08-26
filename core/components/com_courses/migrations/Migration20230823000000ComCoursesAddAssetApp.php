<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing courses tables
 **/
class Migration20230823000000ComCoursesAddAssetApp extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__courses_asset_app'))
		{
			$query = "CREATE TABLE `#__courses_asset_app (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `member_id` int(11) NOT NULL,
			  `asset_id` int(11) NOT NULL,
			  `created` datetime NOT NULL,
			  `passed` tinyint(1) NOT NULL,
			  `details` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__courses_asset_app'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_asset_app`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
