<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for fixing up some support ticket field data types
 **/
class Migration20160808143522ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_tickets'))
		{
			$info = $this->db->getTableColumns('#__support_tickets', false);

			if ($this->db->tableHasField('#__support_tickets', 'owner') && $info['owner']->Null != "NO")
			{
				$query = "ALTER TABLE `#__support_tickets` CHANGE COLUMN `owner` `owner` int(11) NOT NULL DEFAULT ''";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
