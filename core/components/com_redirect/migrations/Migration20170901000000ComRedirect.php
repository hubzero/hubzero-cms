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
 * Migration script for installing redirect tables
 **/
class Migration20170901000000ComRedirect extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__redirect_links'))
		{
			$query = "CREATE TABLE `#__redirect_links` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `old_url` varchar(255) NOT NULL,
			  `new_url` varchar(255) NOT NULL,
			  `referer` varchar(150) NOT NULL,
			  `comment` varchar(255) NOT NULL,
			  `hits` int(10) unsigned NOT NULL DEFAULT '0',
			  `published` tinyint(4) NOT NULL,
			  `created_date` datetime DEFAULT NULL,
			  `modified_date` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_link_old` (`old_url`),
			  KEY `idx_link_modifed` (`modified_date`)
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
		if ($this->db->tableExists('#__redirect_links'))
		{
			$query = "DROP TABLE IF EXISTS `#__redirect_links`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
