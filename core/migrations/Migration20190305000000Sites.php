<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing unused sites table
 **/
class Migration20190305000000Sites extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__sites'))
		{
			$query = "DROP TABLE IF EXISTS `#__sites`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__sites'))
		{
			$query = "CREATE TABLE `#__sites` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `title` varchar(100) DEFAULT NULL,
			  `category` varchar(100) DEFAULT NULL,
			  `url` varchar(255) DEFAULT NULL,
			  `image` varchar(255) DEFAULT NULL,
			  `teaser` varchar(255) DEFAULT NULL,
			  `description` text,
			  `notes` text,
			  `checked_out` int(11) NOT NULL DEFAULT '0',
			  `checked_out_time` DEFAULT NULL,
			  `published` tinyint(1) NOT NULL DEFAULT '0',
			  `published_date`DEFAULT NULL,
			  `state` varchar(30) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
