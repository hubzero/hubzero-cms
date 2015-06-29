<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding rate limiting table
 **/
class Migration20150629100000ComGeosearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__geosearch_markers'))
		{
			$query = "CREATE TABLE `#__geosearch_markers` (
			  `id` int(11) DEFAULT NULL,
			  `scope` varchar(255) DEFAULT NULL,
			  `scope_id` int(11) DEFAULT NULL,
			  `addressLatitude` varchar(255) DEFAULT NULL,
			  `addressLongitude` varchar(255) DEFAULT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__geosearch_markers'))
		{
			$query = "DROP TABLE `#__geosearch_markers`";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
