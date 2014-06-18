<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juri = JURI::getInstance();
$jconfig = JFactory::getConfig();

$st = new SupportTags(JFactory::getDBO());

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

$message  = 'The following is a list of open tickets.' . "\n\n";

if (isset($this->tickets['critical']) && count($this->tickets['critical']) > 0)
{
	$message .= '----------------------------'."\n";
	$message .= 'Critical' . "\n";
	$message .= '----------------------------'."\n\n";

	foreach ($this->tickets['critical'] as $ticket)
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
				$ticket->summary = JText::_('(no content found)');
			}
		}
		$ticket->summary = str_replace("\r", "", $ticket->summary);
		$ticket->summary = str_replace("\t", " ", $ticket->summary);
		$ticket->summary = str_replace("\n", " ", $ticket->summary);

		$sef = JRoute::_($base . $ticket->id);

		$message .= '#' . $ticket->id . ' "' . $ticket->summary . '"' . "\n";
		$message .= rtrim($juri->base(), DS) . DS . trim($sef, DS) . "\n\n";
	}
}

if (isset($this->tickets['major']) && count($this->tickets['major']) > 0)
{
	$message .= '----------------------------'."\n";
	$message .= 'Major' . "\n";
	$message .= '----------------------------'."\n\n";

	foreach ($this->tickets['major'] as $ticket)
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
				$ticket->summary = JText::_('(no content found)');
			}
		}
		$ticket->summary = str_replace("\r", "", $ticket->summary);
		$ticket->summary = str_replace("\t", " ", $ticket->summary);
		$ticket->summary = str_replace("\n", " ", $ticket->summary);

		$sef = JRoute::_($base . $ticket->id);

		$message .= '#' . $ticket->id . ' "' . $ticket->summary . '"' . "\n";
		$message .= rtrim($juri->base(), DS) . DS . trim($sef, DS) . "\n\n";
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
		if (!$ticket->summary)
		{
			$ticket->summary = substr($ticket->report, 0, 70);
			if (strlen($ticket->summary) >= 70)
			{
				$ticket->summary .= '...';
			}
			if (!trim($ticket->summary))
			{
				$ticket->summary = JText::_('(no content found)');
			}
		}
		$ticket->summary = str_replace("\r", "", $ticket->summary);
		$ticket->summary = str_replace("\t", " ", $ticket->summary);
		$ticket->summary = str_replace("\n", " ", $ticket->summary);

		$sef = JRoute::_($base . $ticket->id);

		$message .= '#' . $ticket->id . ' "' . $ticket->summary . '"' . "\n";
		$message .= rtrim($juri->base(), DS) . DS . trim($sef, DS) . "\n\n";
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
