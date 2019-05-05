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
 * Migration script for setting state=3 on reported forum posts
 **/
class Migration20140702115751ComForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableHasField('#__forum_posts', 'state'))
		{
			$query = "SELECT referenceid FROM `#__abuse_reports` WHERE state=0 AND category IN ('forum')";
			$this->db->setQuery($query);
			if ($ids = $this->db->loadColumn())
			{
				$ids = array_map('intval', $ids);

				$query = "UPDATE `#__forum_posts` SET state=3 WHERE id IN (" . implode(',', $ids) . ")";
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
		if ($this->db->tableHasField('#__forum_posts', 'state'))
		{
			$query = "UPDATE `#__forum_posts` SET state=1 WHERE state=3";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
