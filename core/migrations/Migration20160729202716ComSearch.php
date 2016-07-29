<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing search component's default settings.
 **/
class Migration20160729202716ComSearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$params = '{"engine":"basic"}';
		$query = "UPDATE #__extensions SET params='".$params."' WHERE name='com_search';";
		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// No down method
	}
}
