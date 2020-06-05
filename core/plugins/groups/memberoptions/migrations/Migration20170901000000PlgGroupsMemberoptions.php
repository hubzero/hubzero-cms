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
 * Migration script for installing xgroups_memberoptions table
 **/
class Migration20170901000000PlgGroupsMemberoptions extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__xgroups_memberoption'))
		{
			$query = "CREATE TABLE `#__xgroups_memberoption` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `gidNumber` int(11) DEFAULT NULL,
			  `userid` int(11) DEFAULT NULL,
			  `optionname` varchar(100) DEFAULT NULL,
			  `optionvalue` varchar(100) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_gidNumber` (`gidNumber`),
			  KEY `idx_userid` (`userid`)
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
		if ($this->db->tableExists('#__xgroups_memberoption'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_memberoption`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
