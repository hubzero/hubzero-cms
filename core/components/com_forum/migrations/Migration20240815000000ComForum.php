<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding a new table for forum likes
 **/
class Migration20240815000000ComForum extends Base 
{
	public function up()
	{
		// Create table for forum likes.
		if (!$this->db->tableExists('#__forum_posts_like')) 
		{
			$query = "CREATE TABLE `#__forum_posts_like` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `threadId` varchar(255) NOT NULL,
			  `postId` varchar(255) NOT NULL,
			  `userId` varchar(255) NOT NULL,
			  `created` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		$query = "DROP TABLE IF EXISTS `#__forum_posts_like`";
		$this->db->setQuery($query);
		$this->db->query();
	}
}
