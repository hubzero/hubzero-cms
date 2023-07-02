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
class Migration20230628000000ComCoursesAddAssetClips extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__courses_asset_clips'))
		{
			$query = "CREATE TABLE `#__courses_asset_clips` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `scope` varchar(50) NOT NULL DEFAULT 'asset_group',
			  `scope_id` int(11) NOT NULL DEFAULT '0',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `params` text NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_scope` (`scope`),
			  KEY `idx_scope_id` (`scope_id`),
			  KEY `idx_created_by` (`created_by`)
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
		if ($this->db->tableExists('#__courses_asset_clips'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_asset_clips`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
