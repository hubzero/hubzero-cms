<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating table #__tool_handlers
 **/
class Migration20160217014424ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__tool_handlers'))
		{
			$query = "CREATE TABLE `#__tool_handlers` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `tool_id` int(11) NOT NULL,
				  `prompt` varchar(255) NOT NULL DEFAULT '',
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__tool_handler_rules'))
		{
			$query = "CREATE TABLE `#__tool_handler_rules` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `handler_id` int(11) NOT NULL,
				  `extension` varchar(10) NOT NULL DEFAULT '',
				  `quantity` varchar(10) NOT NULL DEFAULT '',
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__tool_handlers'))
		{
			$query = "DROP TABLE `#__tool_handlers`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tool_handler_rules'))
		{
			$query = "DROP TABLE `#__tool_handler_rules`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
