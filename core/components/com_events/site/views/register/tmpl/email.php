<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$message  = Lang::txt('COM_EVENTS_NAME') . ': ' . $this->register['firstname'].' '.$this->register['lastname'] ."\n";
$message .= Lang::txt('COM_EVENTS_TITLE') . ': ' . $this->register['title'] ."\n";
$message .= Lang::txt('COM_EVENTS_AFFILIATION') . ': ' . $this->register['affiliation'] ."\n";
$message .= Lang::txt('COM_EVENTS_EMAIL') . ': ' . $this->register['email'] ."\n";
$message .= Lang::txt('COM_EVENTS_WEBSITE') . ': ' . $this->register['website'] ."\n";
$message .= Lang::txt('COM_EVENTS_PHONE') . ': ' . $this->register['telephone'] ."\n";
$message .= Lang::txt('COM_EVENTS_FAX') . ': ' . $this->register['fax'] ."\n\n";

$message .= Lang::txt('COM_EVENTS_CITY') . ': ' . $this->register['city'] ."\n";
$message .= Lang::txt('COM_EVENTS_STATE') . ': ' . $this->register['state'] ."\n";
$message .= Lang::txt('COM_EVENTS_ZIP') . ': ' . $this->register['postalcode'] ."\n";
$message .= Lang::txt('COM_EVENTS_COUNTRY') . ': ' . $this->register['country'] ."\n\n";

if (isset($this->register['position']) || isset($this->register['position_other']))
{
	$message .= Lang::txt('COM_EVENTS_POSITION') . ': ';
	$message .= ($this->register['position']) ? $this->register['position'] : $this->register['position_other'];
	$message .= "\n\n";
}

if (isset($this->register['degree']))
{
	$message .= Lang::txt('COM_EVENTS_DEGREE') . ': ' . $this->register['degree'] ."\n\n";
}

if (isset($this->register['sex']))
{
	$message .= Lang::txt('COM_EVENTS_GENDER') . ': ' . $this->register['sex'] ."\n\n";
}

if ($this->race)
{
	//$message .= 'Race: '.implode(', ',$race) ."\n\n";
	$message .= Lang::txt('COM_EVENTS_RACE') . ': ';
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
	$message .= Lang::txt('COM_EVENTS_HAS_DISABILITY')."\n\n";
}
else
{
	$message .= Lang::txt('COM_EVENTS_NO_DISABILITY')."\n\n";
}

if (isset($this->dietary['needs']) || (isset($this->dietary['specific']) && $this->dietary['specific'] != ''))
{
	$message .= Lang::txt('COM_EVENTS_HAS_DIETARY', $this->dietary['specific']);
}
else
{
	$message .= Lang::txt('COM_EVENTS_NO_DIETARY')."\n\n";
}

if ($this->arrival)
{
	$message .= Lang::txt('COM_EVENTS_ARRIVAL')."\n";
	$message .= Lang::txt('COM_EVENTS_ARRIVAL_DAY', $this->arrival['day']) ."\n";
	$message .= Lang::txt('COM_EVENTS_ARRIVAL_TIME', $this->arrival['time']) ."\n\n";
}

if ($this->departure)
{
	$message .= Lang::txt('COM_EVENTS_DEPARTURE')."\n";
	$message .= Lang::txt('COM_EVENTS_DEPARTURE_DAY', $this->departure['day']) ."\n";
	$message .= Lang::txt('COM_EVENTS_DEPARTURE_TIME', $this->departure['time']) ."\n\n";
}

if ($this->dinner)
{
	$message .= Lang::txt('COM_EVENTS_ATTENDING_DINNER')."\n\n";
}
else
{
	$message .= Lang::txt('COM_EVENTS_NOT_ATTENDING_DINNER')."\n\n";
}

if (isset($this->register['additional']))
{
	$message .= Lang::txt('COM_EVENTS_ADDITIONAL', $this->register['additional'])."\n\n";
}

if (isset($this->register['comments']))
{
	$message .=  Lang::txt('COM_EVENTS_COMMENTS', $this->register['comments'])."\n\n";
}
echo $message;
