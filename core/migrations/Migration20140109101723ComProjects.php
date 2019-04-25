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
 * Migration script to change column type to TEXT to allow extended blog entry
 **/
class Migration20140109101723ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "ALTER TABLE `#__project_microblog` MODIFY `blogentry` TEXT DEFAULT NULL;";
		$this->db->setQuery($query);
		$this->db->query();
	}
}
