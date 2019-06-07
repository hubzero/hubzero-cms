<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for setting up publication building blocks
 **/
class Migration20140515130000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$queries = array();

		// Add opensource field
		if (!$this->db->tableHasField('#__publication_licenses', 'opensource'))
		{
			$queries[] = "ALTER TABLE `#__publication_licenses` ADD `opensource` tinyint(1) NOT NULL DEFAULT '0';";
		}
		// Add restriction field
		if (!$this->db->tableHasField('#__publication_licenses', 'restriction'))
		{
			$queries[] = "ALTER TABLE `#__publication_licenses` ADD `restriction` varchar(100);";
		}

		// Run queries
		if (count($queries) > 0)
		{
			// Run queries
			foreach ($queries as $query)
			{
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
		$queries = array();

		if ($this->db->tableHasField('#__publication_licenses', 'opensource'))
		{
			$queries[] = "ALTER TABLE `#__publication_licenses` DROP `opensource`";
		}
		if ($this->db->tableHasField('#__publication_licenses', 'restriction'))
		{
			$queries[] = "ALTER TABLE `#__publication_licenses` DROP `restriction`";
		}

		// Run queries
		if (count($queries) > 0)
		{
			// Run queries
			foreach ($queries as $query)
			{
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
