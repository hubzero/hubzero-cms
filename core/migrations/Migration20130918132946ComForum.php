<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding com_forum component entry if missing, or adding admin_menu_link if missing
 **/
class Migration20130918132946ComForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('Forum');

		if ($this->db->tableExists('#__components'))
		{
			$query = "SELECT * FROM `#__components` WHERE `name` = 'Forum'";
			$this->db->setQuery($query);
			$result = $this->db->loadObject();

			if ($result && empty($result->admin_menu_link))
			{
				$query = "UPDATE `#__components` SET `admin_menu_link` = 'option=com_forum' WHERE `id` = '{$result->id}'";
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
		$this->deleteComponentEntry('Forum');
	}
}