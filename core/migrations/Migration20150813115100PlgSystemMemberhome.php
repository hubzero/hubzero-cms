<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding 'member home page' system plugin
 **/
class Migration20150813115100PlgSystemMemberhome extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `folder` = 'system' AND `element` = 'memberhome' AND `type` = 'plugin'";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if (!$id)
			{
				$this->addPluginEntry('system', 'memberhome', 0);
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
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `folder` = 'system' AND `element` = 'memberhome' AND `type` = 'plugin'";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if ($id)
			{
				$this->deletePluginEntry('system', 'memberhome');
			}
		}
	}
}
