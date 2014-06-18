<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for tracking newsletter email bounces
 **/
class Migration20130814185755ComNewsletter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__email_bounces` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`email` varchar(150) DEFAULT NULL,
					`component` varchar(100) DEFAULT NULL,
					`object` varchar(100) DEFAULT NULL,
					`object_id` int(11) DEFAULT NULL,
					`reason` text,
					`date` datetime DEFAULT NULL,
					`resolved` int(11) DEFAULT '0',
					PRIMARY KEY (`id`)
				  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

		$query .= "UPDATE `jos_cron_jobs` SET `params`='' WHERE `plugin`='newsletter' AND `event`='processMailings' AND `params` LIKE '%newsletter_queue_limit=2\n%';";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "DROP TABLE IF EXISTS `#__email_bounces`";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}