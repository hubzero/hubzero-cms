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
 * Migration script for installing #__auth_link_data table
 **/
class Migration20180419000000Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__auth_link_data'))
		{
			$query = "CREATE TABLE `#__auth_link_data` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `link_id` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `domain_key` varchar(255) DEFAULT NULL,
			  `domain_value` text,
			  PRIMARY KEY (`id`),
			  KEY `idx_link_id` (`link_id`)
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
		if ($this->db->tableExists('#__auth_link_data'))
		{
			$query = "DROP TABLE IF EXISTS `#__auth_link_data`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
