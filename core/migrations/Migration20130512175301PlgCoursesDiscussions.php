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
 * Migration script for add watching table
 **/
class Migration20130512175301PlgCoursesDiscussions extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__plugins'))
		{
			$query = "UPDATE `#__plugins` SET `element`='discussions' WHERE `element`='forum' AND `folder`='courses';";
		}
		else
		{
			$query = "UPDATE `#__extensions` SET `element`='discussions' WHERE `element`='forum' AND `folder`='courses';";
		}

		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__plugins'))
		{
			$query = "UPDATE `#__plugins` SET `element`='forum' WHERE `element`='discussions' AND `folder`='courses';";
		}
		else
		{
			$query = "UPDATE `#__extensions` SET `element`='forum' WHERE `element`='discussions' AND `folder`='courses';";
		}

		$this->db->setQuery($query);
		$this->db->query();
	}
}
