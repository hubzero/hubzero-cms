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
 * Migration script to add #__tool_version_zone table
 **/
class Migration20140421135022ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__tool_version_zone'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__tool_version_zone` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `tool_version_id` int(11) NOT NULL,
			  `zone_id` int(11) NOT NULL,
			  `publish_up` datetime DEFAULT NULL,
			  `publish_down` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
