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
 * Migration script for adding 'unique' and 'unfiltered' fields to publication logs table
 **/
class Migration20141119164113ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_logs'))
		{
			if (!$this->db->tableHasField('#__publication_logs', 'page_views_unfiltered'))
			{
				$query = "ALTER TABLE `#__publication_logs` ADD COLUMN page_views_unfiltered int(11);";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__publication_logs', 'primary_accesses_unfiltered'))
			{
				$query = "ALTER TABLE `#__publication_logs` ADD COLUMN primary_accesses_unfiltered int(11);";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__publication_logs', 'page_views_unique'))
			{
				$query = "ALTER TABLE `#__publication_logs` ADD COLUMN page_views_unique int(11);";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if (!$this->db->tableHasField('#__publication_logs', 'primary_accesses_unique'))
			{
				$query = "ALTER TABLE `#__publication_logs` ADD COLUMN primary_accesses_unique int(11);";
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
		if ($this->db->tableExists('#__publication_logs'))
		{
			if ($this->db->tableHasField('#__publication_logs', 'page_views_unfiltered'))
			{
				$query = "ALTER TABLE `#__publication_logs` DROP `page_views_unfiltered`";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__publication_logs', 'primary_accesses_unfiltered'))
			{
				$query = "ALTER TABLE `#__publication_logs` DROP `primary_accesses_unfiltered`";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__publication_logs', 'page_views_unique'))
			{
				$query = "ALTER TABLE `#__publication_logs` DROP `page_views_unique`";
				$this->db->setQuery($query);
				$this->db->query();
			}
			if ($this->db->tableHasField('#__publication_logs', 'primary_accesses_unique'))
			{
				$query = "ALTER TABLE `#__publication_logs` DROP `primary_accesses_unique`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
