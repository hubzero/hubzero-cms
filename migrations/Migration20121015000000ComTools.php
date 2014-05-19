<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding venue tables
 **/
class Migration20121015000000ComTools extends Base
{
	public function up()
	{
		$query = '';

		if (!$this->db->tableExists('#__venue') && !$this->db->tableExists('venue'))
		{
			$query .= "CREATE TABLE IF NOT EXISTS `#__venue` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`venue` varchar(40),
						`network` varchar(40),
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
		}
		if (!$this->db->tableExists('#__venue_countries') && !$this->db->tableExists('venue_countries'))
		{
			$query .= "CREATE TABLE IF NOT EXISTS `#__venue_countries` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`countrySHORT` varchar(40),
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		$query = '';

		if ($this->db->tableExists('#__venue'))
		{
			$query .= "DROP TABLE IF EXISTS `#__venue`;\n";
		}
		if ($this->db->tableExists('#__venue_countries'))
		{
			$query .= "DROP TABLE IF EXISTS `#__venue_countries`;\n";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}