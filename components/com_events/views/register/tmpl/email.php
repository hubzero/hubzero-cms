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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$message  = JText::_('COM_EVENTS_NAME') . ': ' . $this->register['firstname'].' '.$this->register['lastname'] ."\n";
$message .= JText::_('COM_EVENTS_TITLE') . ': ' . $this->register['title'] ."\n";
$message .= JText::_('COM_EVENTS_AFFILIATION') . ': ' . $this->register['affiliation'] ."\n";
$message .= JText::_('COM_EVENTS_EMAIL') . ': ' . $this->register['email'] ."\n";
$message .= JText::_('COM_EVENTS_WEBSITE') . ': ' . $this->register['website'] ."\n";
$message .= JText::_('COM_EVENTS_PHONE') . ': ' . $this->register['telephone'] ."\n";
$message .= JText::_('COM_EVENTS_FAX') . ': ' . $this->register['fax'] ."\n\n";

$message .= JText::_('COM_EVENTS_CITY') . ': ' . $this->register['city'] ."\n";
$message .= JText::_('COM_EVENTS_STATE') . ': ' . $this->register['state'] ."\n";
$message .= JText::_('COM_EVENTS_ZIP') . ': ' . $this->register['postalcode'] ."\n";
$message .= JText::_('COM_EVENTS_COUNTRY') . ': ' . $this->register['country'] ."\n\n";

if (isset($this->register['position']) || isset($this->register['position_other']))
{
	$message .= JText::_('COM_EVENTS_POSITION') . ': ';
	$message .= ($this->register['position']) ? $this->register['position'] : $this->register['position_other'];
	$message .= "\n\n";
}

if (isset($this->register['degree']))
{
	$message .= JText::_('COM_EVENTS_DEGREE') . ': ' . $this->register['degree'] ."\n\n";
}

if (isset($this->register['sex']))
{
	$message .= JText::_('COM_EVENTS_GENDER') . ': ' . $this->register['sex'] ."\n\n";
}

if ($this->race)
{
	//$message .= 'Race: '.implode(', ',$race) ."\n\n";
	$message .= JText::_('COM_EVENTS_RACE') . ': ';
	foreach ($this->race as $r=>$t)
	{
		$message .= ($r != 'nativetribe') ? $r.', ' : '';
	}

	if ($this->race['nativetribe'] != '')
	{
		$message .= $this->race['nativetribe'];
	}
	$message .= "\n\n";
}

if ($this->disability)
{
	$message .= JText::_('COM_EVENTS_HAS_DISABILITY')."\n\n";
}
else
{
	$message .= JText::_('COM_EVENTS_NO_DISABILITY')."\n\n";
}

if (isset($this->dietary['needs']) || (isset($this->dietary['specific']) && $this->dietary['specific'] != ''))
{
	$message .= JText::sprintf('COM_EVENTS_HAS_DIETARY', $this->dietary['specific']);
}
else
{
	$message .= JText::_('COM_EVENTS_NO_DIETARY')."\n\n";
}

if ($this->arrival)
{
	$message .= JText::_('COM_EVENTS_ARRIVAL')."\n";
	$message .= JText::sprintf('COM_EVENTS_ARRIVAL_DAY', $this->arrival['day']) ."\n";
	$message .= JText::sprintf('COM_EVENTS_ARRIVAL_TIME', $this->arrival['time']) ."\n\n";
}

if ($this->departure)
{
	$message .= JText::_('COM_EVENTS_DEPARTURE')."\n";
	$message .= JText::sprintf('COM_EVENTS_DEPARTURE_DAY', $this->departure['day']) ."\n";
	$message .= JText::sprintf('COM_EVENTS_DEPARTURE_TIME', $this->departure['time']) ."\n\n";
}

if ($this->dinner)
{
	$message .= JText::_('COM_EVENTS_ATTENDING_DINNER')."\n\n";
}
else
{
	$message .= JText::_('COM_EVENTS_NOT_ATTENDING_DINNER')."\n\n";
}

if (isset($this->register['additional']))
{
	$message .= JText::sprintf('COM_EVENTS_ADDITIONAL', $this->register['additional'])."\n\n";
}

if (isset($this->register['comments']))
{
	$message .=  JText::sprintf('COM_EVENTS_COMMENTS', $this->register['comments'])."\n\n";
}
echo $message;
