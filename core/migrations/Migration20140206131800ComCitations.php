<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Add a column to store formatted citation in citations table
 **/
class Migration20140206131800ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = '';

		if (!$this->db->tableHasField('#__citations', 'formatted'))
		{
			$query .= "ALTER TABLE `#__citations` ADD COLUMN `formatted` TEXT;";
		}

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
		$query = '';

		if ($this->db->tableHasField('#__citations', 'formatted'))
		{
			$query .= "ALTER TABLE `#__citations` DROP COLUMN `formatted`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
