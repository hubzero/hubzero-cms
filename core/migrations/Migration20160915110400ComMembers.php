<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting access value on accounts that have invalid values (0)
 **/
class Migration201609151104002ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__users'))
		{
			$config = null;
			if ($this->db->tableExists('#__extensions'))
			{
				$query = "SELECT `params` FROM `#__extensions` WHERE `element`='com_members' LIMIT 1";
				$this->db->setQuery($query);
				$config = $this->db->loadResult();
			}

			$access = 1;
			if ($config)
			{
				$config = json_decode($config);
				if (is_object($config))
				{
					$access = (int)$config->privacy;
					$access = $access ?: 1;
				}
			}

			$query = "UPDATE `#__users` SET `access`=$access WHERE `access`=0";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// No down
	}
}
