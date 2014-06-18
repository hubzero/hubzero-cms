<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juri = JURI::getInstance();
$jconfig = JFactory::getConfig();

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=ticket&id=';

$message = '----------------------------'."\n\n";

$cells = array();
$cells[] = '"Number"';
$cells[] = '"Issue"';
$cells[] = '"Created"';
$cells[] = '"Creator"';
$cells[] = '"Assigned"';
$cells[] = '"Severity"';
$cells[] = '"URL"';

$message .= implode(", ", $cells) . "\n";

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
				$ticket->summary = JText::_('(no content found)');
			}
		}
		$ticket->summary = str_replace("\r", "", $ticket->summary);
		$ticket->summary = str_replace("\t", " ", $ticket->summary);
		$ticket->summary = str_replace("\n", " ", $ticket->summary);

		$sef = JRoute::_($base . $ticket->id);

		$cells = array();
		$cells[] = '"#' . addslashes($ticket->id) . '"';
		$cells[] = '"' . addslashes($ticket->summary) . '"';
		$cells[] = '"' . addslashes($ticket->created) . '"';
		$cells[] = '"' . ($ticket->name ? addslashes($ticket->name) : '--') . ($ticket->login ? ' (' . addslashes($ticket->login) . ')' : ' (--)') . '"';
		$cells[] = '"' . ($ticket->owner ? addslashes($ticket->owner_name . ' (' . $ticket->owner . ')') : '--') . '"';
		$cells[] = '"' . addslashes($ticket->severity) . '"';
		$cells[] = '"' . rtrim($juri->base(), DS) . DS . trim($sef, DS) . '"';

		$message .= implode(", ", $cells) . "\n";
	}
}

$message .= '----------------------------'."\n";

echo $message . "\n";
