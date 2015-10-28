<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding an index to the GeoSearch table 
 **/
class Migration20151028151331ComGeosearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
			// Make an auto-incrementing index as ID
			$query = "ALTER TABLE `#__geosearch_markers` MODIFY COLUMN id INT AUTO_INCREMENT, ADD PRIMARY KEY (id);";
			$this->db->setQuery($query);
			$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// Removes the index 
		$query = "ALTER TABLE `#__geosearch_markers` MODIFY id INT NOT NULL;";
		$this->db->setQuery($query);
		$this->db->query();

		// Drop the key
		$query = "ALTER TABLE `#__geosearch_markers` DROP PRIMARY KEY;";
		$this->db->setQuery($query);
		$this->db->query();
	}
}
