<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$message = Lang::txt('EVENTS_REGISTRATION_CONFIRMATION') . "\n\n";

$message .= "----------------------------------------\n";
$message .= $this->eventTitle . "\n";
$message .= "----------------------------------------\n\n";
$message .= Lang::txt('EVENTS_CAL_LANG_EVENT_STARTTIME') . ': ' . $this->eventStart . "\n";
$message .= Lang::txt('EVENTS_CAL_LANG_EVENT_ENDTIME') . ': ' . $this->eventEnd . "\n\n";

$message .= Lang::txt('COM_EVENTS_NAME') . ': ' . $this->register['firstname'] . ' ' . $this->register['lastname'] ."\n";

if ($this->params->get('show_title') && !empty($this->register['title']))
{
	$message .= Lang::txt('COM_EVENTS_TITLE') . ': ' . $this->register['title'] . "\n";
}

if ($this->params->get('show_affiliation') && !empty($this->register['affiliation']))
{
	$message .= Lang::txt('COM_EVENTS_AFFILIATION') . ': ' . $this->register['affiliation'] . "\n";
}

if ($this->params->get('show_email'))
{
	// Required field when configured to show, so always display in confirmation email
	$message .= Lang::txt('COM_EVENTS_EMAIL') . ': ' . $this->register['email'] . "\n";
}

if ($this->params->get('show_website') && !empty($this->register['website']))
{
	$message .= Lang::txt('COM_EVENTS_WEBSITE') . ': ' . $this->register['website'] . "\n";
}

if ($this->params->get('show_telephone') && !empty($this->register['telephone']))
{
	$message .= Lang::txt('COM_EVENTS_PHONE') . ': ' . $this->register['telephone'] . "\n";
}

if ($this->params->get('show_fax') && !empty($this->register['fax']))
{
	$message .= Lang::txt('COM_EVENTS_FAX') . ': ' . $this->register['fax'] . "\n\n";
}

if ($this->params->get('show_address'))
{
	if (!empty($this->register['city']))
	{
		$message .= Lang::txt('COM_EVENTS_CITY') . ': ' . $this->register['city'] . "\n";
	}
	if (!empty($this->register['state']))
	{
		$message .= Lang::txt('COM_EVENTS_STATE') . ': ' . $this->register['state'] . "\n";
	}
	if (!empty($this->register['postalcode']))
	{
		$message .= Lang::txt('COM_EVENTS_ZIP') . ': ' . $this->register['postalcode'] . "\n";
	}
	if (!empty($this->register['country']))
	{
		$message .= Lang::txt('COM_EVENTS_COUNTRY') . ': ' . $this->register['country'] . "\n";
	}
	$message .= "\n";
}

if ($this->params->get('show_position') && (!empty($this->register['position']) || !empty($this->register['position_other'])))
{
	$message .= Lang::txt('COM_EVENTS_POSITION') . ': ';
	$message .= ($this->register['position']) ? ucfirst($this->register['position']) : $this->register['position_other'];
	$message .= "\n\n";
}

if ($this->params->get('show_degree') && !empty($this->register['degree']))
{
	$message .= Lang::txt('COM_EVENTS_DEGREE') . ': ' . ucfirst($this->register['degree']) . "\n\n";
}

if ($this->params->get('show_gender') && !empty($this->register['sex']))
{
	// NOTE: 'gender' and 'sex' seem to refer to the same thing
	$message .= Lang::txt('COM_EVENTS_GENDER') . ': ' . ucfirst($this->register['sex']) . "\n\n";
}

if ($this->params->get('show_race') && $this->race)
{
	$message .= Lang::txt('COM_EVENTS_RACE') . ': ';
	foreach ($this->race as $r => $t)
	{
		$message .= ($r != 'nativetribe') ? ucfirst($r) . ', ' : '';
	}

	if (!empty($this->race['nativetribe']))
	{
		$message .= $this->race['nativetribe'];
	}

	if (substr($message, -2) === ', ')
	{
		$message = substr($message, 0, -2);
	}

	$message .= "\n\n";
}

if ($this->params->get('show_disability'))
{
	if ($this->disability)
	{
		$message .= Lang::txt('COM_EVENTS_HAS_DISABILITY');
	}
	else
	{
		$message .= Lang::txt('COM_EVENTS_NO_DISABILITY');
	}
	$message .= "\n\n";
}

if ($this->params->get('show_dietary'))
{
	if (!empty($this->dietary['needs']) || !empty($this->dietary['specific']))
	{
		$message .= Lang::txt('COM_EVENTS_HAS_DIETARY', $this->dietary['specific']);
	}
	else
	{
		$message .= Lang::txt('COM_EVENTS_NO_DIETARY');
	}
	$message .= "\n\n";
}

if ($this->params->get('show_arrival') && $this->arrival && (!empty($this->arrival['day']) || !empty($this->arrival['time'])))
{
	$message .= Lang::txt('COM_EVENTS_ARRIVAL') . "\n";
	$message .= Lang::txt('COM_EVENTS_ARRIVAL_DAY', $this->arrival['day']) . "\n";
	$message .= Lang::txt('COM_EVENTS_ARRIVAL_TIME', $this->arrival['time']) . "\n\n";
}

if ($this->params->get('show_departure') && $this->departure && (!empty($this->departure['day']) || !empty($this->departure['time'])))
{
	$message .= Lang::txt('COM_EVENTS_DEPARTURE') . "\n";
	$message .= Lang::txt('COM_EVENTS_DEPARTURE_DAY', $this->departure['day']) . "\n";
	$message .= Lang::txt('COM_EVENTS_DEPARTURE_TIME', $this->departure['time']) . "\n\n";
}

if ($this->params->get('show_dinner'))
{
	if ($this->dinner)
	{
		$message .= Lang::txt('COM_EVENTS_ATTENDING_DINNER');
	}
	else
	{
		$message .= Lang::txt('COM_EVENTS_NOT_ATTENDING_DINNER');
	}
	$message .= "\n\n";
}

if ($this->params->get('show_abstract') && !empty($this->register['additional']))
{
	// NOTE: 'abstract' and 'additional' seem to refer to the same thing
	$message .= Lang::txt('COM_EVENTS_ADDITIONAL', $this->register['additional']) . "\n\n";
}

if ($this->params->get('show_comments') && !empty($this->register['comments']))
{
	$message .=  Lang::txt('COM_EVENTS_COMMENTS', $this->register['comments']) . "\n\n";
}

echo $message;
