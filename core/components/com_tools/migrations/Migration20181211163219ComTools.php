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
 * Migration to fix old weber publish settings
 **/

class Migration20181211163219ComTools extends Base
{
	/**
	 * Up;
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__tool_version'))
		{
			$query = "SELECT `id`, `params` FROM `#__tool_version` WHERE `params` LIKE '%weber=true%'";

			$this->db->setQuery($query);
			$rows = $this->db->loadObjectList();

			foreach ($rows as $row)
			{
				if (!trim($row->params))
				{
					continue;
				}

				$params = '{"github":"","publishType":"weber="}';

				$query = "UPDATE `#__tool_version` SET `params`=" . $this->db->quote($params) . " WHERE `id`=" . $this->db->quote($row->id);
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
		// None
	}
}
