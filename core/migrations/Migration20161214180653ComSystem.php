<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for help to correct default value for GeoDB
 **/
class Migration20161214180653ComSystem extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$sql = "SELECT params FROM #__extensions WHERE name = 'com_system';";
		$this->db->setQuery($sql);
		$params = $this->db->query()->loadResult();

		$params = json_decode($params);
		$params->geodb_driver = 'pdo';
		$params = json_encode($params);
		$params = $this->db->quote($params);

		$sql1 = "UPDATE #__extensions SET params={$params} WHERE name = 'com_system';";
		$this->db->setQuery($sql1);
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$sql = "SELECT params FROM #__extensions WHERE name = 'com_system';";
		$this->db->setQuery($sql);
		$params = $this->db->query()->loadResult();

		$params = json_decode($params);
		$params->geodb_driver = 'mysql';
		$params = json_encode($params);
		$params = $this->db->quote($params);

		$sql1 = "UPDATE #__extensions SET params={$params} WHERE name = 'com_system';";
		$this->db->setQuery($sql1);
		$this->db->query();

	}
}
