<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for tracking when linked accounts are created
 **/
class Migration20130403000000Core extends Base
{
	public function up()
	{
		$query = '';

		if (!$this->db->tableHasField('#__auth_link', 'linked_on'))
		{
			$query .= "ALTER TABLE `#__auth_link` ADD COLUMN `linked_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;";
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

		if ($this->db->tableHasField('#__auth_link', 'linked_on'))
		{
			$query .= "ALTER TABLE `#__auth_link` DROP COLUMN `linked_on`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}