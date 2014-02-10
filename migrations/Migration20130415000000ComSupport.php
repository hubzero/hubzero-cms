<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130415000000ComSupport extends Migration
{
	protected static function up($db)
	{
		if (!$db->tableHasField('#__support_tickets', 'closed'))
		{
			// Add a unique index on grade book and asset_id field to forms table
			$query = "ALTER TABLE `#__support_tickets` ADD `closed` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00' AFTER `created`";
			$db->setQuery($query);
			$db->query();

			// Closed tickets
			$sql = "SELECT c.ticket, c.created
					FROM `#__support_comments` AS c 
					LEFT JOIN `#__support_tickets` AS t ON c.ticket=t.id
					WHERE t.open=0 
					ORDER BY c.created ASC";

			$db->setQuery($sql);
			$clsd = $db->loadObjectList();

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
				$query = "UPDATE `#__support_tickets` SET `closed`=" . $db->Quote($closed) . " WHERE id=" . $db->Quote($ticket);

				$db->setQuery($query);
				$db->query();
			}
		}
	}

	protected function down($db)
	{
		$query = "ALTER TABLE `#__support_tickets` DROP `closed`;";

		$db->setQuery($query);
		$db->query();
	}
}