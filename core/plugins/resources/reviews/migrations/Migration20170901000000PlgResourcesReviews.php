<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing resource reviews table
 **/
class Migration20170901000000PlgResourcesReviews extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__resource_ratings'))
		{
			$query = "CREATE TABLE `#__resource_ratings` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `resource_id` int(11) NOT NULL DEFAULT '0',
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
			  `comment` text NOT NULL,
			  `created` datetime DEFAULT NULL,
			  `anonymous` tinyint(3) NOT NULL DEFAULT '0',
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__resource_ratings'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_ratings`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
