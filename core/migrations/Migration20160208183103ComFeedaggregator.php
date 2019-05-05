<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for converting the timestamps in the created field to
 * standard format
 **/

class Migration20160208183103ComFeedaggregator extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__feedaggregator_posts'))
		{
			// Grab rows first
			$query = "SELECT * FROM `#__feedaggregator_posts`;";
			$this->db->setQuery($query);
			$rows = $this->db->loadObjectList();

			// Convert the field
			$query = "ALTER TABLE `#__feedaggregator_posts` MODIFY created DATETIME;";
			$this->db->setQuery($query);
			$this->db->query();

			// Convert each timestamp into SQL date format
			foreach ($rows as $row)
			{
				$dt = \Date::of(date("F j, Y, g:i a", $row->created))->toSql();
				$query = "UPDATE `#__feedaggregator_posts` SET `created`=" . $this->db->quote($dt) . " WHERE `id`=" . $this->db->quote($row->id) . ";";
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
		if ($this->db->tableExists('#__feedaggregator_posts'))
		{
			// Grab rows first
			$query = "SELECT * FROM `#__feedaggregator_posts`;";
			$this->db->setQuery($query);
			$rows = $this->db->loadObjectList();

			// Convert the field
			$query = "ALTER TABLE `#__feedaggregator_posts` MODIFY created INT(11);";
			$this->db->setQuery($query);
			$this->db->query();

			// Convert each timestamp into SQL date format
			foreach ($rows as $row)
			{
				$dt = \Date::of($row->created)->toUnix();
				$query = "UPDATE `#__feedaggregator_posts` SET `created`=" . $this->db->quote($dt) . " WHERE `id`=" . $this->db->quote($row->id) . ";";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
