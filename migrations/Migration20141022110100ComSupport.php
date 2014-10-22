<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing some display issues with old support tickets
 **/
class Migration20141022110100ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_tickets'))
		{
			$this->db->setQuery("SELECT id, report FROM `#__support_tickets` WHERE `created` < '2013-01-01 00:00:00' AND `report` LIKE '%\\\\\'%' AND `type`=0 AND `open`=1");
			if ($records = $this->db->loadObjectList())
			{
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'ticket.php');

				// Update the query
				foreach ($records as $record)
				{
					$row = new SupportTicket($this->db);
					$row->bind($record);
					$row->report = str_replace('&quot;', '"', $row->report);
					$row->report = stripslashes($row->report);
					$row->report = html_entity_decode($row->report);
					$row->summary = substr($row->report, 0, 70);
					if (strlen($row->summary) >=70)
					{
						$row->summary .= '...';
					}
					$row->store();
				}
			}
		}
	}
}