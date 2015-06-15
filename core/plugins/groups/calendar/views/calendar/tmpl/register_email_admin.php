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

$message  = 'Group "' . $this->group->get('description') . '" event registration: "' . $this->event->title . '"' . "\n\n";
$message .= 'http://' . $_SERVER['HTTP_HOST'] . DS . 'groups' . DS . $this->group->get('cn') . DS . 'calendar' . DS . 'details' . DS . $this->event->id;
$message .= "\n\n" . '-------------------------------------------------------------------' . "\n\n";

$message .= 'Name: '. $this->register['first_name'].' '.$this->register['last_name'] ."\n";
if ($this->params->get('show_title', 1) == 1 && isset($this->register['title']))
{
	$message .= 'Title: '. $this->register['title'] ."\n";
}
if ($this->params->get('show_affiliation', 1) == 1 && isset($this->register['affiliation']))
{
	$message .= 'Affiliation: '. $this->register['affiliation'] ."\n";
}
if ($this->params->get('show_email', 1) == 1 && isset($this->register['email']))
{
	$message .= 'Email: '. $this->register['email'] ."\n";
}
if ($this->params->get('show_website', 1) == 1 && isset($this->register['website']))
{
	$message .= 'Website: '. $this->register['website'] ."\n";
}
if ($this->params->get('show_telephone', 1) == 1 && isset($this->register['telephone']))
{
	$message .= 'Telephone: '. $this->register['telephone'] ."\n";
}
if ($this->params->get('show_fax', 1) == 1 && isset($this->register['fax']))
{
	$message .= 'Fax: '. $this->register['fax'] ."\n\n";
}
if ($this->params->get('show_address', 1) == 1)
{
	if (isset($this->register['city']))
	{
		$message .= 'City: '. $this->register['city'] ."\n";
	}
	if (isset($this->register['state']))
	{
		$message .= 'State/Province: '. $this->register['state'] ."\n";
	}
	if (isset($this->register['zip']))
	{
		$message .= 'Zip/Postal Code: '. $this->register['zip'] ."\n";
	}
	if (isset($this->register['country']))
	{
		$message .= 'Country: '. $this->register['country'] ."\n\n";
	}
}



if ($this->params->get('show_position', 1) == 1 && (isset($this->register['position']) || isset($this->register['position_other']))) {
	$message .= 'Current position: ';
	$message .= ($this->register['position']) ? $this->register['position'] : $this->register['position_other'];
	$message .= "\n\n";
}
if ($this->params->get('show_degree', 1) == 1 && isset($this->register['degree']))
{
	$message .= 'Highest degree earned: '. $this->register['degree'] ."\n\n";
}
if ($this->params->get('show_gender', 1) == 1 && isset($this->register['sex']))
{
	$message .= 'Gender: '. $this->register['sex'] ."\n\n";
}
if ($this->params->get('show_race', 1) == 1 && isset($this->race))
{
	if (isset($this->race['nativetribe']))
	{
		$tribe = $this->race['nativetribe'];
		unset($this->race['nativetribe']);
	}
	$message .= 'Race: '.implode(', ',$this->race);
	$message .= ($tribe != '') ? ', '.$tribe : '';
	$message .= "\n\n";
}

if ($this->params->get('show_disability', 1) == 1)
{
	if ($this->disability)
	{
		$message .= '[X] I have auxiliary aids or services due to a disability. Please contact me.'."\n\n";
	}
	else
	{
		$message .= '[ ] I have auxiliary aids or services due to a disability. Please contact me.'."\n\n";
	}
}
if ($this->params->get('show_dietary', 1) == 1)
{
	if (isset($this->dietary['needs']) || (isset($this->dietary['specific']) && $this->dietary['specific'] != ''))
	{
		$message .= '[X] I have specific dietary needs.'."\n\n";
		$message .= '    Specific: '.$this->dietary['specific']."\n\n";
	}
	else
	{
		$message .= '[ ] I have specific dietary needs.'."\n\n";
	}
}

if ($this->params->get('show_arrival', 1) == 1 && $this->arrival)
{
	$message .= '=== Arrival ==='."\n";
	$message .= 'Arrival Day: '. $this->arrival['day'] ."\n";
	$message .= 'Arrival Time: '. $this->arrival['time'] ."\n\n";
}
if ($this->params->get('show_departure', 1) == 1 && $this->departure)
{
	$message .= '=== Departure ==='."\n";
	$message .= 'Departure Day: '. $this->departure['day'] ."\n";
	$message .= 'Departure Time: '. $this->departure['time'] ."\n\n";
}

if ($this->params->get('show_dinner', 1) == 1)
{
	if ($this->dinner)
	{
		$message .= '[x] Attending dinner.'."\n\n";
	}
	else
	{
		$message .= '[ ] Attending dinner.'."\n\n";
	}
}

if ($this->params->get('show_abstract', 1) == 1 && isset($this->register['abstract']))
{
	$message .= 'Abstract: '. $this->register['abstract'] ."\n\n";
}
if ($this->params->get('show_comments', 1) == 1 && isset($this->register['comment']))
{
	$message .= 'Comments: '. $this->register['comment'] ."\n\n";
}
echo $message;
