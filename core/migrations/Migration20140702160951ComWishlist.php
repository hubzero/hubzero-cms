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
 * Migration script for setting status=7 on reported wishes
 **/
class Migration20140702160951ComWishlist extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableHasField('#__wishlist_item', 'status'))
		{
			$query = "SELECT referenceid FROM `#__abuse_reports` WHERE state=0 AND category IN ('wish')";
			$this->db->setQuery($query);
			if ($ids = $this->db->loadColumn())
			{
				$ids = array_map('intval', $ids);

				$query = "UPDATE `#__wishlist_item` SET status=7 WHERE id IN (" . implode(',', $ids) . ")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableHasField('#__item_comments', 'state'))
		{
			$query = "SELECT referenceid FROM `#__abuse_reports` WHERE state=0 AND category IN ('itemcomment', 'wishcomment')";
			$this->db->setQuery($query);
			if ($ids = $this->db->loadColumn())
			{
				$ids = array_map('intval', $ids);

				$query = "UPDATE `#__item_comments` SET state=3 WHERE id IN (" . implode(',', $ids) . ")";
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
		if ($this->db->tableHasField('#__wishlist_item', 'status'))
		{
			$query = "UPDATE `#__wishlist_item` SET status=0 WHERE status=7";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__item_comments', 'state'))
		{
			$query = "UPDATE `#__item_comments` SET state=1 WHERE state=3";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
