<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for dropping feature history table
 **/
class Migration20140416082740ComFeatures extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteComponentEntry('features');

		if ($this->db->tableExists('#__feature_history'))
		{
			$query = "DROP TABLE IF EXISTS `#__feature_history`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addComponentEntry('features');

		if (!$this->db->tableExists('#__feature_history'))
		{
			$query = "CREATE TABLE `#__feature_history` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `objectid` int(11) DEFAULT NULL,
				  `featured` datetime DEFAULT '0000-00-00 00:00:00',
				  `tbl` varchar(255) DEFAULT NULL,
				  `note` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MYISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
