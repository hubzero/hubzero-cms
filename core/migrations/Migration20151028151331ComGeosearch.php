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
		if ($this->db->tableExists('#__geosearch_markers'))
		{
			$query = "SHOW INDEX FROM `#__geosearch_markers`";
			$this->db->setQuery($query);
			$this->db->query();
			$key = $this->db->loadAssoc();

			if ($key == NULL || $key['Key_name'] != 'PRIMARY' && $key['column'] =! 'id')
			{
				// Make an auto-incrementing index as ID
				$query = "ALTER TABLE `#__geosearch_markers` MODIFY COLUMN id INT AUTO_INCREMENT, ADD PRIMARY KEY (id);";
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
		if ($this->db->tableExists('#__geosearch_markers'))
		{
			$query = "SHOW INDEX FROM `#__geosearch_markers`";
			$this->db->setQuery($query);
			$this->db->query();
			$key = $this->db->loadAssoc();

			if ($key['Key_name'] == 'PRIMARY' && $key['column'] =! 'id')
			{
				// Removes the index
				$query = "ALTER TABLE `#__geosearch_markers` MODIFY id INT NOT NULL;";
				$this->db->setQuery($query);
				if ($this->db->query())
				{
					// Drop the key
					$query = "ALTER TABLE `#__geosearch_markers` DROP PRIMARY KEY;";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}
