<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding extension entry for admin mod_whosonline
 **/
class Migration20150521150730ModWhosonline extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `element` = 'mod_whosonline' AND `client_id` = 1";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if (!$id)
			{
				$this->addModuleEntry('mod_whosonline', 1, '', 1);
			}
			else
			{
				// Set the first zone as default
				$query = "UPDATE `#__extensions` SET `state`=0 WHERE `extension_id` = " . $this->db->quote($id);
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
		$this->deleteModuleEntry('mod_whosonline', 1);
	}
}