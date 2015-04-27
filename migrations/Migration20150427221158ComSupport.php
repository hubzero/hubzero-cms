<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for assigning appropriate status for closed tickets
 **/
class Migration20150427221158ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_tickets') && $this->db->tableExists('#__support_statuses'))
		{
			$query = "SELECT id FROM `#__support_statuses` WHERE `open`=1";
			$this->db->setQuery($query);
			$open = $this->db->loadResultArray();

			if (count($open))
			{
				$query = "UPDATE `#__support_tickets` SET `status`=0 WHERE `open`=0 AND `status` IN (" . implode(',', $open) . ")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}