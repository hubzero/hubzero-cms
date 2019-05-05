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
 * Migration script for adding state column to publication ratings table
 **/
class Migration20150518190000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_ratings'))
		{
			if (!$this->db->tableHasField('#__publication_ratings', 'state'))
			{
				$query = "ALTER TABLE `#__publication_ratings` ADD COLUMN state tinyint(2) NOT NULL DEFAULT '1'";
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
		if ($this->db->tableExists('#__publication_ratings'))
		{
			if ($this->db->tableHasField('#__publication_ratings', 'state'))
			{
				$query = "ALTER TABLE `#__publication_ratings` DROP `state`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
