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
 * Migration script for making default collections private.
 *
 * This is a follow-up migration to an earlier one that did
 * not take into account records where access was set to "1"
 * by Joomla.
 **/
class Migration20150106211443ComCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__collections'))
		{
			$query = "UPDATE `#__collections` SET access=4 WHERE is_default=1 AND access=1 AND created < '2014-06-30 00:00:00'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__collections'))
		{
			$query = "UPDATE `#__collections` SET access=0 WHERE is_default=1 AND access=4 AND created < '2014-06-30 00:00:00'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
