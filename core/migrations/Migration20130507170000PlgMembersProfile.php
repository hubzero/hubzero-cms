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
 * Migration script for new member address fields
 **/
class Migration20130507170000PlgMembersProfile extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = '';

		// create event calendars table
		if (!$this->db->tableExists('#__xprofiles_address'))
		{
			$query .= "CREATE TABLE `#__xprofiles_address` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`uidNumber` int(11) DEFAULT NULL,
						`addressTo` varchar(200) DEFAULT NULL,
						`address1` varchar(255) DEFAULT NULL,
						`address2` varchar(255) DEFAULT NULL,
						`addressCity` varchar(200) DEFAULT NULL,
						`addressRegion` varchar(200) DEFAULT NULL,
						`addressPostal` varchar(200) DEFAULT NULL,
						`addressCountry` varchar(200) DEFAULT NULL,
						`addressLatitude` float DEFAULT NULL,
						`addressLongitude` float DEFAULT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
