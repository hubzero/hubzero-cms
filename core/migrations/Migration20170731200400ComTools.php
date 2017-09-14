<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for fixing group_id value for tool tickets
 **/
class Migration20170731200400ComTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_tickets')
		 && $this->db->tableHasField('#__support_tickets', 'group_id')
		 && $this->db->tableExists('#__xgroups')
		 && $this->db->tableHasField('#__xgroups', 'gidNumber')
		 && $this->db->tableHasField('#__xgroups', 'cn')
		 && $this->db->tableExists('#__tool')
		 && $this->db->tableHasField('#__tool', 'toolname')
		 && $this->db->tableHasField('#__tool', 'ticketid'))
		{
			$prefix = 'app-';

			if ($this->db->tableExists('#__extensions'))
			{
				$query = "SELECT `params` FROM `#__extensions` WHERE `element`=" . $this->db->quote('com_tools');
				$this->db->setQuery($query);
				$params = $this->db->loadResult();
				if ($params && substr($params, 0, 1) == '{')
				{
					$params = json_decode($params);
					$prefix = $params->group_prefix;
				}
			}

			$query = "SELECT t.toolname, t.ticketid FROM `#__tool` AS t
				INNER JOIN `#__support_tickets` AS st ON st.id=t.ticketid
				WHERE st.group_id=0";
			$this->db->setQuery($query);
			$tools = $this->db->loadObjectList();

			foreach ($tools as $tool)
			{
				$query = "SELECT `gidNumber` FROM `#__xgroups` WHERE `cn`=" . $this->db->quote($prefix . $tool->toolname);
				$this->db->setQuery($query);
				$gidNumber = $this->db->loadResult();

				if ($gidNumber)
				{
					$query = "UPDATE `#__support_tickets` SET `group_id`=" . $this->db->quote($gidNumber) . " WHERE `id`=" . $this->db->quote($tool->ticketid);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// No down. Just fixing incorrect data in up().
	}
}
