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
 * Migration script for adding FULLTEXT indexes to publication versions
 **/
class Migration20141028094100ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableHasField('#__publication_versions', 'title')
			&& !$this->db->tableHasKey('#__publication_versions', 'ftidx_title'))
		{
			$query = "ALTER TABLE `#__publication_versions` ADD FULLTEXT INDEX `ftidx_title` (`title` ASC)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__publication_versions', 'abstract')
			&& $this->db->tableHasField('#__publication_versions', 'description')
			&& !$this->db->tableHasKey('#__publication_versions', 'ftidx_abstract_description'))
		{
			$query = "ALTER TABLE `#__publication_versions` ADD FULLTEXT INDEX `ftidx_abstract_description` (`abstract` ASC, `description` ASC)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasKey('#__publication_versions', 'ftidx_title_abstract_description')
			&& $this->db->tableHasField('#__publication_versions', 'title')
			&& $this->db->tableHasField('#__publication_versions', 'abstract')
			&& $this->db->tableHasField('#__publication_versions', 'description'))
		{
			$query = "ALTER TABLE `#__publication_versions` ADD FULLTEXT INDEX `ftidx_title_abstract_description` (`title` ASC, `abstract` ASC, `description` ASC)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasKey('#__publication_versions', 'ftidx_title'))
		{
			$query = "ALTER TABLE `#__publication_versions` DROP INDEX `ftidx_title`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasKey('#__publication_versions', 'ftidx_abstract_description'))
		{
			$query = "ALTER TABLE `#__publication_versions` DROP INDEX `ftidx_abstract_description`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasKey('#__publication_versions', 'ftidx_title_abstract_description'))
		{
			$query = "ALTER TABLE `#__publication_versions` DROP INDEX `ftidx_title_abstract_description`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
