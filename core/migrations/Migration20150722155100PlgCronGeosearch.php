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
 * Migration script for adding GeoSearch cron plugin.
 **/
class Migration20150722155100PlgCronGeosearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `folder` = 'cron' AND `element` = 'geosearch' AND `type` = 'plugin'";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if (!$id)
			{
				$this->addPluginEntry('cron', 'geosearch');
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `folder` = 'cron' AND `element` = 'geosearch' AND `type` = 'plugin'";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if ($id)
			{
				$this->deletePluginEntry('cron', 'geosearch');
			}
		}
	}
}
