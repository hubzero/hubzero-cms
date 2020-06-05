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
 * Migration script for installing course notes table
 **/
class Migration20170901000000PlgCoursesNotes extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__courses_member_notes'))
		{
			$query = "CREATE TABLE `#__courses_member_notes` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `scope` varchar(255) NOT NULL DEFAULT '',
			  `scope_id` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `content` mediumtext NOT NULL,
			  `pos_x` int(11) NOT NULL DEFAULT '0',
			  `pos_y` int(11) NOT NULL DEFAULT '0',
			  `width` int(11) NOT NULL DEFAULT '0',
			  `height` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  `timestamp` time NOT NULL DEFAULT '00:00:00',
			  `section_id` int(11) NOT NULL DEFAULT '0',
			  `access` tinyint(2) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_scoped` (`scope`,`scope_id`),
			  KEY `idx_createdby` (`created_by`)
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
		if ($this->db->tableExists('#__courses_member_notes'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_member_notes`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
