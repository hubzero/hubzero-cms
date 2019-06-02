<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$bdcolor = array(
	'critical' => '#e9bcbc',
	'major'    => '#e9e1bc',
	'normal'   => '#e1e1e1',
	'minor'    => '#bccbe9',
	'trivial'  => '#bce1e9'
);
$bgcolor = array(
	'critical' => '#ffd3d4',
	'major'    => '#fbf1be',
	'normal'   => '#f1f1f1',
	'minor'    => '#d3e3ff',
	'trivial'  => '#d3f9ff'
);
$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=ticket&id=';
$site = rtrim(Request::base(), '/');

$message  = 'The following is a list of open tickets.' . "\n\n";

if (isset($this->tickets['critical']) && count($this->tickets['critical']) > 0)
{
	$message .= '----------------------------'."\n";
	$message .= 'Critical' . "\n";
	$message .= '----------------------------'."\n\n";

	foreach ($this->tickets['critical'] as $ticket)
	{
		if (!$this->config->get('email_terse'))
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
		}
		else
		{
			$ticket->summary = Lang::txt('COM_SUPPORT_TICKET');
		}

		$sef = Route::url($base . $ticket->id);
		if (substr($site, -13) == 'administrator')
		{
			$sef = 'support/ticket/' . $ticket->id;
		}
		$link = $site . '/' . trim($sef, '/');
		$link = str_replace('/administrator', '', $link);

		$message .= '#' . $ticket->id . ' "' . $ticket->summary . '"' . "\n";
		$message .= $link . "\n\n";
	}
}

if (isset($this->tickets['major']) && count($this->tickets['major']) > 0)
{
	$message .= '----------------------------'."\n";
	$message .= 'Major' . "\n";
	$message .= '----------------------------'."\n\n";

	foreach ($this->tickets['major'] as $ticket)
	{
		if (!$this->config->get('email_terse'))
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
		}
		else
		{
			$ticket->summary = Lang::txt('COM_SUPPORT_TICKET');
		}

		$sef = Route::url($base . $ticket->id);
		if (substr($site, -13) == 'administrator')
		{
			$sef = 'support/ticket/' . $ticket->id;
		}
		$link = $site . '/' . trim($sef, '/');
		$link = str_replace('/administrator', '', $link);

		$message .= '#' . $ticket->id . ' "' . $ticket->summary . '"' . "\n";
		$message .= $link . "\n\n";
	}
}

$message .= '----------------------------'."\n\n";

$more = 0;
$i = 0;
foreach ($this->tickets as $severity => $tickets)
{
	if ($severity == 'critical' || $severity == 'major')
	{
		continue;
	}
	// Add the ticket count to the total
	$more += count($tickets);
	if ($i >= 5)
	{
		continue;
	}

	foreach ($tickets as $ticket)
	{
		if (!$this->config->get('email_terse'))
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
		}
		else
		{
			$ticket->summary = Lang::txt('COM_SUPPORT_TICKET');
		}

		$sef = Route::url($base . $ticket->id);
		if (substr($site, -13) == 'administrator')
		{
			$sef = 'support/ticket/' . $ticket->id;
		}
		$link = $site . '/' . trim($sef, '/');
		$link = str_replace('/administrator', '', $link);

		$message .= '#' . $ticket->id . ' "' . $ticket->summary . '"' . "\n";
		$message .= $link . "\n\n";
		$i++;
		// Subtract one from total for each ticket passed
		$more--;
	}
}
if ($more)
{
	$message .= '... and ' . $more . ' more open tickets.' . "\n";
}

echo $message . "\n";
