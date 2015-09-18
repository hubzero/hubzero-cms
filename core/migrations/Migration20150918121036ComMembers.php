<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding registration reasons table and default values
 **/
class Migration20150918121036ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__xprofiles_reasons'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__xprofiles_reasons` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`reason` varchar(255) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xprofiles_reasons'))
		{
			$query = "SELECT COUNT(*) FROM `#__xprofiles_reasons`";
			$this->db->setQuery($query);
			$rows = $this->db->loadResult();
			if (!$rows)
			{
				$query = "INSERT INTO `#__xprofiles_reasons` (`id`, `reason`)
						VALUES
							(1,'Required for class'),
							(2,'Developing a new course'),
							(3,'Using in an existing course'),
							(4,'Using simulation tools for research'),
							(5,'Using as background for my research'),
							(6,'Learning about subject matter'),
							(7,'Keeping current in subject matter');";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__xprofiles_reasons'))
		{
			$query = "DROP TABLE IF EXISTS `#__xprofiles_reasons`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}