<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding citations sponsers
 **/
class Migration20130404000000ComCitations extends Base
{
	public function up()
	{
		$query = '';

		if (!$this->db->tableHasField('#__citations_sponsors', 'image'))
		{
			$query .= "ALTER TABLE `#__citations_sponsors` ADD COLUMN `image` VARCHAR(200);";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		$query = '';

		if ($this->db->tableHasField('#__citations_sponsors', 'image'))
		{
			$query .= "ALTER TABLE `#__citations_sponsors` DROP COLUMN `image`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}