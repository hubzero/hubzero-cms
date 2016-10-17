<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding tables and data for profile schema
 **/
class Migration20161017133711ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "UPDATE #__user_profile_fields SET action_edit=0 WHERE action_create=0 AND action_update=0 AND name IN ('countryresident','countryorigin','race');";
		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "UPDATE #__user_profile_fields SET action_edit=2 WHERE action_create=0 AND action_update=0 AND name IN ('countryresident','countryorigin','race');";
		$this->db->setQuery($query);
		$this->db->query();
	}
}
