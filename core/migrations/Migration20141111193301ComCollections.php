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
 * Migration script for adding ordering column to collection posts and sort to collections
 **/
class Migration20141111193301ComCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__collections'))
		{
			if (!$this->db->tableHasField('#__collections', 'sort'))
			{
				$query = "ALTER TABLE `#__collections` ADD `sort` VARCHAR(50) NOT NULL DEFAULT 'created';";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__collections', 'layout'))
			{
				$query = "ALTER TABLE `#__collections` ADD `layout` VARCHAR(50) NOT NULL DEFAULT 'grid';";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__collections_posts'))
		{
			if (!$this->db->tableHasField('#__collections_posts', 'ordering'))
			{
				$query = "ALTER TABLE `#__collections_posts` ADD `ordering` int(11) NOT NULL DEFAULT '0';";
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
		if ($this->db->tableExists('#__collections'))
		{
			if ($this->db->tableHasField('#__collections', 'sort'))
			{
				$query = "ALTER TABLE `#__collections` DROP `sort`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__collections', 'layout'))
			{
				$query = "ALTER TABLE `#__collections` DROP `layout`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__collections_posts'))
		{
			if ($this->db->tableHasField('#__collections_posts', 'ordering'))
			{
				$query = "ALTER TABLE `#__collections_posts` DROP `ordering`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
