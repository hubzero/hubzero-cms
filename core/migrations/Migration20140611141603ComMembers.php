<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding ORCID field to profiles
 **/
class Migration20140611141603ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xprofiles'))
		{
			if (!$this->db->tableHasField('#__xprofiles', 'orcid'))
			{
				$query = "ALTER TABLE `#__xprofiles` ADD `orcid` VARCHAR(255)  NOT NULL  DEFAULT '';";
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
		if ($this->db->tableExists('#__xprofiles'))
		{
			if ($this->db->tableHasField('#__xprofiles', 'orcid'))
			{
				$query = "ALTER TABLE `#__xprofiles` DROP COLUMN `orcid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}