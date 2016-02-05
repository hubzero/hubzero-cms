<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding a review point column
 **/
class Migration201602051319501ComGeosearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// If the table does not have the `review` field
		if (!$this->db->tableHasField('#__geosearch_markers', 'review'))
		{
			// A handmade SQL statement
			$query = "ALTER TABLE #__geosearch_markers ADD COLUMN review boolean";

			// Set the query
			$this->db->setQuery($query);

			// Run the query, no need for output gathering
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// If the table does not have the `review` field
		if ($this->db->tableHasField('#__geosearch_markers', 'review'))
		{
			// A handmade SQL statement
			$query = "ALTER TABLE #__geosearch_markers DROP COLUMN review";

			// Set the query
			$this->db->setQuery($query);

			// Run the query, no need for output gathering
			$this->db->query();
		}
	}
}
