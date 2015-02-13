<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for creating table jos_xprofiles_tokens for password reset tokens
 **/
class Migration20150210203958ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__xprofiles_tokens'))
		{
			$query = "CREATE TABLE `#__xprofiles_tokens` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `token` varchar(100) NOT NULL DEFAULT '',
				  `user_id` int(11) NOT NULL,
				  `created` datetime NOT NULL,
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
		if ($this->db->tableExists('#__xprofiles_tokens'))
		{
			$query = "DROP TABLE `#__xprofiles_tokens`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}