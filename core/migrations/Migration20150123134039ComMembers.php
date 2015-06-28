<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add #__users_quotas_classes_groups table
 **/
class Migration20150123134039ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__users_quotas_classes_groups'))
		{
			$query = "CREATE TABLE `#__users_quotas_classes_groups` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `class_id` int(11) unsigned NOT NULL DEFAULT '0',
				  `group_id` int(11) unsigned NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  KEY `idx_class_id` (`class_id`),
				  KEY `idx_group_id` (`group_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__users_quotas_classes_groups'))
		{
			$query = "DROP TABLE `#__users_quotas_classes_groups`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}