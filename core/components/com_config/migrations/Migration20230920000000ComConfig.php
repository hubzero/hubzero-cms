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
 * Migration script for managing hub secret via com_config
 **/
class Migration20230920000000ComConfig extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// ensure we have the needed database table:
		if (!$this->db->tableExists('campaign_hub'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `campaign_hub` (
				`secret` CHAR(32) UNIQUE NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		// ensure we have a single record in the table:
		if ($this->db->tableExists('campaign_hub'))
		{
			$query = "SELECT count(*) FROM `campaign_hub`;";
			$this->db->setQuery($query);
			$count = $this->db->loadResult();

			if ($count == 0)
			{
				$query = "INSERT INTO `campaign_hub` (`secret`) VALUES ('A3J8vNM4c38a56ROEdkeJVOI4hld10kD');";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('campaign_hub'))
		{
			$query = "DROP TABLE IF EXISTS `campaign_hub`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
