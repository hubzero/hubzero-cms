<?php

use Hubzero\Content\Migration\Base;

// no direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding indexes to the `#__auth_domain` table.
 **/
class Migration20200917100000AuthDomainIndex extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__auth_domain') &&
			!$this->db->tableHasKey('#__auth_domain', 'authenticator_idx'))
		{
			$query = "ALTER TABLE `#__auth_domain` ADD INDEX authenticator_idx (authenticator);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__auth_domain') &&
			$this->db->tableHasKey('#__auth_domain', 'authenticator_idx'))
		{
			$query = "ALTER TABLE `#__auth_domain` DROP INDEX authenticator_idx;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
