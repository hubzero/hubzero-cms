<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

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
				$path = PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'ticket.php';
				if (!file_exists($path))
				{
					$path = PATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'ticket.php';
				}
				include_once($path);

				// Update the query
				foreach ($records as $record)
				{
					if (class_exists('SupportTicket'))
					{
						$row = new SupportTicket($this->db);
					}
					else
					{
						$row = new \Components\Support\Tables\Ticket($this->db);
					}
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