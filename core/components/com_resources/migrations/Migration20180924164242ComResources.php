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
 * Migration to add primary key to `#__resource_stats_tools_topvals` table
 **/

class Migration20180924164242ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__resource_stats_tools_topvals'))
		{
			if ($this->db->getPrimaryKey('#__resource_stats_tools_topvals') != 'id')
			{
				$query = "ALTER TABLE `#__resource_stats_tools_topvals` ADD PRIMARY KEY (id)";
				//$this->db->setQuery($query);
				//$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__resource_stats_tools_topvals'))
		{
			if ($this->db->getPrimaryKey('#__resource_stats_tools_topvals') == 'id')
			{
				$query = "ALTER TABLE `#__resource_stats_tools_topvals` DROP PRIMARY KEY";
				//$this->db->setQuery($query);
				//$this->db->query();
			}
		}
	}
}
