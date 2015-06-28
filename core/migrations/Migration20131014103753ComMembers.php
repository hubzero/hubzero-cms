<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding members quota interface
 **/
class Migration20131014103753ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__users_quotas'))
		{
			$query = "CREATE TABLE `#__users_quotas` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`user_id` int(11) NOT NULL,
						`class_id` int(11) DEFAULT NULL,
						`hard_files` int(11) NOT NULL,
						`soft_files` int(11) NOT NULL,
						`hard_blocks` int(11) NOT NULL,
						`soft_blocks` int(11) NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__users_quotas_classes'))
		{
			$query = "CREATE TABLE `#__users_quotas_classes` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`alias` varchar(255) NOT NULL DEFAULT '',
						`hard_files` int(11) NOT NULL,
						`soft_files` int(11) NOT NULL,
						`hard_blocks` int(11) NOT NULL,
						`soft_blocks` int(11) NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();

			$query = "INSERT INTO `#__users_quotas_classes` (`id`, `alias`, `hard_files`, `soft_files`, `hard_blocks`, `soft_blocks`) VALUES (1, 'default', 0, 0, 1000000, 900000);";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__users_quotas_log'))
		{
			$query = "CREATE TABLE `#__users_quotas_log` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`object_type` varchar(255) NOT NULL DEFAULT '',
						`object_id` int(11) NOT NULL,
						`name` varchar(255) NOT NULL DEFAULT '',
						`action` varchar(255) NOT NULL DEFAULT '',
						`actor_id` int(11) NOT NULL,
						`soft_blocks` int(11) NOT NULL,
						`hard_blocks` int(11) NOT NULL,
						`soft_files` int(11) NOT NULL,
						`hard_files` int(11) NOT NULL,
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
		if ($this->db->tableExists('#__users_quotas'))
		{
			$query = "DROP TABLE `#__users_quotas`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__users_quotas_classes'))
		{
			$query = "DROP TABLE `#__users_quotas_classes`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__users_quotas_log'))
		{
			$query = "DROP TABLE `#__users_quotas_log`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}