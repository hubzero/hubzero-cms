<?php

class ComSupport20130415 extends Hubzero_Migration
{
	protected function up()
	{
		// Add a unique index on grade book and asset_id field to forms table
		$query = "ALTER TABLE `jos_support_tickets` ADD `closed` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00' AFTER `created`";

		$this->get('db')->exec($query);

		// Closed tickets
		$sql = "SELECT c.ticket, c.created
				FROM jos_support_comments AS c 
				LEFT JOIN jos_support_tickets AS t ON c.ticket=t.id
				WHERE t.open=0 
				ORDER BY c.created ASC";
		$result = $this->get('db')->query($sql);
		$clsd   = $result->fetchAll(PDO::FETCH_ASSOC);

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
			//$this->get('db')->exec("UPDATE `jos_support_tickets` SET `closed`=" . $this->get('db')->Quote($closed) . " WHERE id=" . $this->get('db')->Quote($ticket));
			$stmt = $this->get('db')->prepare("UPDATE `jos_support_tickets` SET `closed`= ? WHERE id = ? ");
			$stmt->execute(array($closed, $ticket));
		}
	}

	protected function down()
	{
		$query = "ALTER TABLE `jos_support_tickets` DROP `closed`;";

		$this->get('db')->exec($query);
	}
}