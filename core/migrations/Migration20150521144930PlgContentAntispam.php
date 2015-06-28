<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding basic antispam plugin.
 **/
class Migration20150521144930PlgContentAntispam extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `folder` = 'content' AND `element` = 'antispam' AND `type` = 'plugin'";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if (!$id)
			{
				$this->addPluginEntry('content', 'antispam');
			}
			else
			{
				// Set the first zone as default
				$query = "UPDATE `#__extensions` SET `state`=0 AND `name`='plg_content_antispam' WHERE `extension_id` = " . $this->db->quote($id);
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
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `folder` = 'content' AND `element` = 'antispam' AND `type` = 'plugin'";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if ($id)
			{
				$this->deletePluginEntry('content', 'antispam');
			}
		}
	}
}