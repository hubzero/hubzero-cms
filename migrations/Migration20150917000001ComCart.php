<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script to create the downloads log table
 **/
class Migration20150917000001ComCart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__cart_downloads'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__cart_downloads` (
				  `dId` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				  `uId` INT(11) DEFAULT NULL,
				  `sId` INT(11) DEFAULT NULL,
				  `dDownloaded` DATETIME DEFAULT NULL,
				  PRIMARY KEY (`dId`) )
				ENGINE = MyISAM
				DEFAULT CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		if ($this->db->tableExists('#__cart_downloads'))
		{
			$query = "DROP TABLE IF EXISTS `#__cart_downloads`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

}