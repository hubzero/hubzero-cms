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
 * Migration script for installing resources sponsors table
 **/
class Migration20170901000000PlgResourcesSponsors extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__resource_sponsors'))
		{
			$query = "CREATE TABLE `#__resource_sponsors` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `alias` varchar(255) DEFAULT NULL,
			  `title` varchar(255) DEFAULT NULL,
			  `state` tinyint(3) NOT NULL DEFAULT '1',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  `description` text,
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
		if ($this->db->tableExists('#__resource_sponsors'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_sponsors`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
