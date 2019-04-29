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
 * Migration script for installing projects todo table
 **/
class Migration20170901000000PlgProjectsTodo extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__project_todo'))
		{
			$query = "CREATE TABLE `#__project_todo` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `projectid` int(11) NOT NULL DEFAULT '0',
			  `todolist` varchar(255) DEFAULT NULL,
			  `created` datetime NOT NULL,
			  `duedate` datetime DEFAULT NULL,
			  `closed` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `assigned_to` int(11) DEFAULT '0',
			  `closed_by` int(11) DEFAULT '0',
			  `priority` int(11) DEFAULT '0',
			  `activityid` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(1) NOT NULL DEFAULT '0',
			  `milestone` tinyint(1) NOT NULL DEFAULT '0',
			  `private` tinyint(1) NOT NULL DEFAULT '0',
			  `details` text,
			  `content` varchar(255) NOT NULL,
			  `color` varchar(20) DEFAULT NULL,
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
		if ($this->db->tableExists('#__project_todo'))
		{
			$query = "DROP TABLE IF EXISTS `#__project_todo`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
