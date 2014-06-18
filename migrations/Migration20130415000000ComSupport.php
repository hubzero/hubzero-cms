<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for support ticket closed date
 **/
class Migration20130415000000ComSupport extends Base
{
	public function up()
	{
		if (!$this->db->tableHasField('#__support_tickets', 'closed'))
		{
			// Add a unique index on grade book and asset_id field to forms table
			$query = "ALTER TABLE `#__support_tickets` ADD `closed` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00' AFTER `created`";
			$this->db->setQuery($query);
			$this->db->query();

			// Closed tickets
			$sql = "SELECT c.ticket, c.created
					FROM `#__support_comments` AS c
					LEFT JOIN `#__support_tickets` AS t ON c.ticket=t.id
					WHERE t.open=0
					ORDER BY c.created ASC";

			$this->db->setQuery($sql);
			$clsd = $this->db->loadObjectList();

			// First we need to loop through all the entries and reove some potential duplicates
			$closedTickets = array();
			foreach ($clsd as $closed)
			{
				if (!isset($closedTickets[$closed->ticket]))
				{
					$closedTickets[$closed->ticket] = $closed->created;
				}
				else
				{
					if ($closedTickets[$closed->ticket] < $closed->created)
					{
						$closedTickets[$closed->ticket] = $closed->created;
					}
				}
			}

			foreach ($closedTickets as $ticket => $closed)
			{
				$query = "UPDATE `#__support_tickets` SET `closed`=" . $this->db->Quote($closed) . " WHERE id=" . $this->db->Quote($ticket);

				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	public function down()
	{
		$query = "ALTER TABLE `#__support_tickets` DROP `closed`;";

		$this->db->setQuery($query);
		$this->db->query();
	}
}