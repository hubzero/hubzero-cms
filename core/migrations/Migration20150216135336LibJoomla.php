<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding protected and private access levels
 **/
class Migration20150216135336LibJoomla extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__viewlevels'))
		{
			$query = "SELECT `ordering` FROM `#__viewlevels` ORDER BY `ordering` DESC LIMIT 1";
			$this->db->setQuery($query);
			$i = (int) $this->db->loadResult();
			if ($i == 2)
			{
				foreach (array('Protected' => '[1]', 'Private' => '[8]') as $title => $usergroups)
				{
					$i++;

					$query = "INSERT INTO `#__viewlevels` (`id`, `title`, `ordering`, `rules`) VALUES (null, " . $this->db->quote($title) . "," . $i . "," . $this->db->quote($usergroups) . ")";
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
		if ($this->db->tableExists('#__viewlevels'))
		{
			$query = "DELETE FROM `#__viewlevels` WHERE `title` IN (" . $this->db->quote('Protected') . "," . $this->db->quote('Private') . ")";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}