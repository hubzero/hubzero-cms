<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=ticket&id=';

$cells = array();
$cells[] = '"Number"';
$cells[] = '"Issue"';
$cells[] = '"Created"';
$cells[] = '"Creator"';
$cells[] = '"Assigned"';
$cells[] = '"Severity"';
$cells[] = '"URL"';

$message = implode(", ", $cells) . "\n";

if (isset($this->tickets) && count($this->tickets) > 0)
{
	foreach ($this->tickets as $ticket)
	{
		if (!$ticket->summary)
		{
			$ticket->summary = substr($ticket->report, 0, 70);
			if (strlen($ticket->summary) >= 70)
			{
				$ticket->summary .= '...';
			}
			if (!trim($ticket->summary))
			{
				$ticket->summary = Lang::txt('(no content found)');
			}
		}
		$ticket->summary = str_replace("\r", "", $ticket->summary);
		$ticket->summary = str_replace("\t", " ", $ticket->summary);
		$ticket->summary = str_replace("\n", " ", $ticket->summary);

		$sef = Route::url($base . $ticket->id);

		$cells = array();
		$cells[] = '"#' . addslashes($ticket->id) . '"';
		$cells[] = '"' . addslashes($ticket->summary) . '"';
		$cells[] = '"' . addslashes($ticket->created) . '"';
		$cells[] = '"' . ($ticket->name ? addslashes($ticket->name) : '--') . ($ticket->login ? ' (' . addslashes($ticket->login) . ')' : ' (--)') . '"';
		$cells[] = '"' . ($ticket->owner ? addslashes($ticket->owner_name . ' (' . $ticket->owner . ')') : '--') . '"';
		$cells[] = '"' . addslashes($ticket->severity) . '"';
		$cells[] = '"' . rtrim(Request::base(), '/') . '/' . trim($sef, '/') . '"';

		$message .= implode(", ", $cells) . "\n";
	}
}

echo $message . "\n";
