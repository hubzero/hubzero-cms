<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for renaming admin menu module
 **/
class Migration20150121104223ModMenu extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__modules'))
		{
			$query = "UPDATE `#__modules` SET `module`=" . $this->db->quote('mod_menu') . " WHERE `client_id`=1 AND `module`=" . $this->db->quote('mod_hubmenu');
			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->deleteModuleEntry('mod_hubmenu');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addModuleEntry('mod_hubmenu', 1, '', 1);
	}
}