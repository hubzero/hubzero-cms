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
 * Migration script for adding fulltext key to collections tables
 **/
class Migration20131106152023ComCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__collections_items')
			&& !$this->db->tableHasKey('#__collections_items', 'idx_fulltxt_title_description')
			&& $this->db->tableHasField('#__collections_items', 'title')
			&& $this->db->tableHasField('#__collections_items', 'description'))
		{
			$query = "ALTER TABLE `#__collections_items` ADD FULLTEXT KEY `idx_fulltxt_title_description` (`title`, `description`);";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__collections_posts')
			&& !$this->db->tableHasKey('#__collections_posts', 'idx_fulltxt_description')
			&& $this->db->tableHasField('#__collections_posts', 'description'))
		{
			$query = "ALTER TABLE `#__collections_posts` ADD FULLTEXT KEY `idx_fulltxt_description` (`description`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__collections_items') && $this->db->tableHasKey('#__collections_items', 'idx_fulltxt_title_description'))
		{
			$query = "ALTER TABLE `#__collections_items` DROP INDEX `idx_fulltxt_title_description`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__collections_posts') && $this->db->tableHasKey('#__collections_posts', 'idx_fulltxt_description'))
		{
			$query = "ALTER TABLE `#__collections_posts` DROP INDEX `idx_fulltxt_description`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
