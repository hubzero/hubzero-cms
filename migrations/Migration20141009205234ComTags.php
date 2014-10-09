<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing up tags tag field data type
 **/
class Migration20141009205234ComTags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__tags'))
		{
			$info = $this->db->getTableColumns('#__tags', false);

			if ($this->db->tableHasField('#__tags', 'tag') && $info['tag']->Null != "NO")
			{
				$query = "ALTER TABLE `#__tags` CHANGE COLUMN `tag` `tag` VARCHAR(100) NOT NULL DEFAULT '' ";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}