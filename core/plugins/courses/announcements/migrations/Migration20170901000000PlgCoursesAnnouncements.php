<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing course announcements table
 **/
class Migration20170901000000PlgCoursesAnnouncements extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__courses_announcements'))
		{
			$query = "CREATE TABLE `#__courses_announcements` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `offering_id` int(11) NOT NULL DEFAULT '0',
			  `content` text,
			  `priority` tinyint(2) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `section_id` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  `publish_up` datetime DEFAULT NULL,
			  `publish_down` datetime DEFAULT NULL,
			  `sticky` tinyint(2) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_offering_id` (`offering_id`),
			  KEY `idx_section_id` (`section_id`),
			  KEY `idx_created_by` (`created_by`),
			  KEY `idx_state` (`state`),
			  KEY `idx_priority` (`priority`)
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
		if ($this->db->tableExists('#__courses_announcements'))
		{
			$query = "DROP TABLE IF EXISTS `#__courses_announcements`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
