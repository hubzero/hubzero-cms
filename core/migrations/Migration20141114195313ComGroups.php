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
 * Migration script for adding id field to groups members table
 **/
class Migration20141114195313ComGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xgroups_members'))
		{
			if (!$this->db->tableHasField('#__xgroups_members', 'id'))
			{
				$keys    = $this->db->getTableKeys('#__xgroups_members');
				$primary = false;
				if ($keys && count($keys) > 0)
				{
					foreach ($keys as $key)
					{
						if ($key->Key_name == "PRIMARY")
						{
							$primary = true;
						}
					}

					if ($primary)
					{
						$query = "ALTER TABLE `#__xgroups_members` DROP PRIMARY KEY";
						$this->db->setQuery($query);
						$this->db->query();
					}
				}

				$query = "ALTER TABLE `#__xgroups_members` ADD COLUMN id SERIAL NOT NULL PRIMARY KEY FIRST";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
