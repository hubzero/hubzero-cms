<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Hubzero!
defined('_HZEXEC_') or die();

/**
 * Migration script for renaming admin modules to avoid conflict with site modules
 **/
class Migration20150623105812Mod extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "UPDATE `#__extensions` SET `element`=" . $this->db->quote('mod_adminmenu') . " WHERE `client_id`=1 AND `element`=" . $this->db->quote('mod_menu');
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__extensions` SET `element`=" . $this->db->quote('mod_adminlogin') . " WHERE `client_id`=1 AND `element`=" . $this->db->quote('mod_login');
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__modules'))
		{
			$query = "UPDATE `#__modules` SET `module`=" . $this->db->quote('mod_adminmenu') . " WHERE `client_id`=1 AND `module`=" . $this->db->quote('mod_menu');
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__modules` SET `module`=" . $this->db->quote('mod_adminlogin') . " WHERE `client_id`=1 AND `module`=" . $this->db->quote('mod_login');
			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->deleteModuleEntry('mod_dashboard', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "UPDATE `#__extensions` SET `element`=" . $this->db->quote('mod_menu') . " WHERE `client_id`=1 AND `element`=" . $this->db->quote('mod_adminmenu');
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__extensions` SET `element`=" . $this->db->quote('mod_login') . " WHERE `client_id`=1 AND `element`=" . $this->db->quote('mod_adminlogin');
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__modules'))
		{
			$query = "UPDATE `#__modules` SET `module`=" . $this->db->quote('mod_menu') . " WHERE `client_id`=1 AND `module`=" . $this->db->quote('mod_adminmenu');
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__modules` SET `module`=" . $this->db->quote('mod_login') . " WHERE `client_id`=1 AND `module`=" . $this->db->quote('mod_adminlogin');
			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->addModuleEntry('mod_dashboard', 1, '', 1);
	}
}