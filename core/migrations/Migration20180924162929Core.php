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
 * Migration to add id and primary key to `#__stats_topvals` table
 **/

class Migration20180924162929Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__stats_topvals'))
		{
			if (!$this->db->tableHasField('#__stats_topvals', 'id'))
			{
				$query = "ALTER TABLE `#__stats_topvals` ADD `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
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
		if ($this->db->tableExists('#__stats_topvals'))
		{
			if ($this->db->tableHasField('#__stats_topvals', 'id'))
			{
				$query = "ALTER TABLE `#__stats_topvals` DROP `id`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
