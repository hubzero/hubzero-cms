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
 * Add a column to store formatted citation in citations table
 **/
class Migration20140206141200ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = '';

		if (!$this->db->tableHasField('#__citations', 'format'))
		{
			$query .= "ALTER TABLE `#__citations` ADD COLUMN `format` VARCHAR(11);";
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

		if ($this->db->tableHasField('#__citations', 'format'))
		{
			$query .= "ALTER TABLE `#__citations` DROP COLUMN `format`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
