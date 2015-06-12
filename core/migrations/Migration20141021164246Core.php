<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for making sure auto_increment values are sufficiently high
 **/
class Migration20141021164246Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__users'))
		{
			$auto = $this->db->getAutoIncrement('#__users');

			if ($auto && is_numeric($auto) && $auto < 1000)
			{
				$query = "ALTER TABLE `#__users` AUTO_INCREMENT = " . ($auto+1000);
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__xgroups'))
		{
			$auto = $this->db->getAutoIncrement('#__xgroups');

			if ($auto && is_numeric($auto) && $auto < 1000)
			{
				$query = "ALTER TABLE `#__xgroups` AUTO_INCREMENT = " . ($auto+1000);
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__extensions'))
		{
			$auto = $this->db->getAutoIncrement('#__extensions');

			if ($auto && is_numeric($auto) && $auto < 10000)
			{
				$query = "ALTER TABLE `#__extensions` AUTO_INCREMENT = " . ($auto+10000);
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}