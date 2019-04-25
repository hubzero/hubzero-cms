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
 * Migration script for adding id column to dashboard preferences
 **/
class Migration20140508122800PlgMembersDashboard extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__xprofiles_dashboard_preferences', 'id'))
		{
			$query  = "ALTER TABLE `#__xprofiles_dashboard_preferences` ADD COLUMN `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (id);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
