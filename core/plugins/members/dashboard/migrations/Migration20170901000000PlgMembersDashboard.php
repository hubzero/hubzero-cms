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
 * Migration script for installing member dashboard table
 **/
class Migration20170901000000PlgMembersDashboard extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__xprofiles_dashboard_preferences'))
		{
			$query = "CREATE TABLE `#__xprofiles_dashboard_preferences` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uidNumber` int(11) unsigned NOT NULL,
			  `preferences` text,
			  `modified` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidNumber` (`uidNumber`)
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
		if ($this->db->tableExists('#__xprofiles_dashboard_preferences'))
		{
			$query = "DROP TABLE IF EXISTS `#__xprofiles_dashboard_preferences`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
