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
 * Migration script for distinguishing between unanswered and no in profile mail preference column
 **/
class Migration20130618144751PlgMembersProfile extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query  = "ALTER TABLE `#__xprofiles` ALTER COLUMN `mailPreferenceOption` SET DEFAULT -1;";
		$query .= "UPDATE `#__xprofiles` SET `mailPreferenceOption`=1 WHERE `mailPreferenceOption`=2;";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
