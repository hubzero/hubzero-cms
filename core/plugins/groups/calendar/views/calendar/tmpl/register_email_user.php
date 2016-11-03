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

$eventLink = 'http://' . $_SERVER['HTTP_HOST'] . DS . 'groups' . DS . $this->group->get('cn') . DS . 'calendar' . DS . 'details' . DS . $this->event->id;

$message  = 'Thank you for registering for the "' . $this->event->title . '" event. (' . $eventLink . ')';
$message .= "\n\n" . '-------------------------------------------------------------------' . "\n\n";
$message .= 'Below are your registration details:';
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
