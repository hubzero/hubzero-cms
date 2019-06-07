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
 * Migration script for installing com_messages tables
 **/
class Migration20170901000000ComMessages extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__messages'))
		{
			$query = "CREATE TABLE `#__messages` (
			  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id_from` int(10) unsigned NOT NULL DEFAULT '0',
			  `user_id_to` int(10) unsigned NOT NULL DEFAULT '0',
			  `folder_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
			  `date_time` datetime DEFAULT NULL,
			  `state` tinyint(1) NOT NULL DEFAULT '0',
			  `priority` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `subject` varchar(255) NOT NULL DEFAULT '',
			  `message` text NOT NULL,
			  PRIMARY KEY (`message_id`),
			  KEY `useridto_state` (`user_id_to`,`state`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__messages_cfg'))
		{
			$query = "CREATE TABLE `#__messages_cfg` (
			  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
			  `cfg_name` varchar(100) NOT NULL DEFAULT '',
			  `cfg_value` varchar(255) NOT NULL DEFAULT '',
			  UNIQUE KEY `idx_user_var_name` (`user_id`,`cfg_name`)
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
		if ($this->db->tableExists('#__messages'))
		{
			$query = "DROP TABLE IF EXISTS `#__messages`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__messages_cfg'))
		{
			$query = "DROP TABLE IF EXISTS `#__messages_cfg`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
