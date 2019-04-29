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
 * Migration script for installing resource recommendation table
 **/
class Migration20170901000000PlgResourcesRecommendations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__recommendation'))
		{
			$query = "CREATE TABLE `#__recommendation` (
			  `fromID` int(11) NOT NULL,
			  `toID` int(11) NOT NULL,
			  `contentScore` float unsigned zerofill DEFAULT NULL,
			  `tagScore` float unsigned zerofill DEFAULT NULL,
			  `titleScore` float unsigned zerofill DEFAULT NULL,
			  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  PRIMARY KEY (`fromID`,`toID`)
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
		if ($this->db->tableExists('#__recommendation'))
		{
			$query = "DROP TABLE IF EXISTS `#__recommendation`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
