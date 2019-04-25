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
class Migration20130521160001ComForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "UPDATE `#__forum_posts` SET `thread`=id WHERE `scope` IN ('site', 'group') AND `parent`=0;";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "UPDATE `#__forum_posts` SET `thread`=parent WHERE `scope` IN ('site', 'group') AND `parent`!=0;";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
