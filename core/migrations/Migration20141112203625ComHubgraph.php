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
 * Migration script for setting up hubgraph
 **/
class Migration20141112203625ComHubgraph extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('hg_update_queue'))
		{
			$query = "CREATE TABLE `hg_update_queue` (
					  `action` enum('INSERT','UPDATE','DELETE') NOT NULL,
					  `table_name` varchar(50) NOT NULL,
					  `id` int(11) NOT NULL,
					  `other_id` int(11) DEFAULT NULL,
					  `note` text
					) ENGINE=InnoDB DEFAULT CHARSET=utf8";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('hg_update_queue'))
		{
			$query = "DROP TABLE `hg_update_queue`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
