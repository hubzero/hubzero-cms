<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to rename production_collections primary key
 **/
class Migration20160825000001ComCart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__cart_meta'))
		{
			$query = "CREATE TABLE `#__cart_meta` (
						`mtId` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`scope_id` int(11) NOT NULL DEFAULT '0',
						`scope` varchar(100) NOT NULL DEFAULT '',
			  			`mtKey` varchar(100) NOT NULL DEFAULT '',
			  			`mtValue` TEXT DEFAULT '',
			  PRIMARY KEY (`mtId`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "DROP TABLE IF EXISTS `#__cart_meta`";
		$this->db->setQuery($query);
		$this->db->query();
	}
}
