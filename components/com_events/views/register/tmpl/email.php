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

$message  = 'Name: '. $this->register['firstname'].' '.$this->register['lastname'] ."\n";
$message .= 'Title: '. $this->register['title'] ."\n";
$message .= 'Affiliation: '. $this->register['affiliation'] ."\n";
$message .= 'Email: '. $this->register['email'] ."\n";
$message .= 'Website: '. $this->register['website'] ."\n";
$message .= 'Telephone: '. $this->register['telephone'] ."\n";
$message .= 'Fax: '. $this->register['fax'] ."\n\n";

$message .= 'City: '. $this->register['city'] ."\n";
$message .= 'State/Province: '. $this->register['state'] ."\n";
$message .= 'Zip/Postal Code: '. $this->register['postalcode'] ."\n";
$message .= 'Country: '. $this->register['country'] ."\n\n";

if (isset($this->register['position']) || isset($this->register['position_other'])) {
	$message .= 'Current position: ';
	$message .= ($this->register['position']) ? $this->register['position'] : $this->register['position_other'];
	$message .= "\n\n";
}
if (isset($this->register['degree'])) {
	$message .= 'Highest degree earned: '. $this->register['degree'] ."\n\n";
}
if (isset($this->register['sex'])) {
	$message .= 'Gender: '. $this->register['sex'] ."\n\n";
}
if ($this->race) {
	//$message .= 'Race: '.implode(', ',$race) ."\n\n";
	$message .= 'Race: ';
	foreach ($this->race as $r=>$t)
	{
		$message .= ($r != 'nativetribe') ? $r.', ' : '';
	}
	if ($this->race['nativetribe'] != '') {
		$message .= $this->race['nativetribe'];
	}
	$message .= "\n\n";
}

if ($this->disability) {
	$message .= '[X] I have auxiliary aids or services due to a disability. Please contact me.'."\n\n";
} else {
	$message .= '[ ] I have auxiliary aids or services due to a disability. Please contact me.'."\n\n";
}
if (isset($this->dietary['needs']) || (isset($this->dietary['specific']) && $this->dietary['specific'] != '')) {
	$message .= '[X] I have specific dietary needs.'."\n\n";
	$message .= '    Specific: '.$this->dietary['specific']."\n\n";
} else {
	$message .= '[ ] I have specific dietary needs.'."\n\n";
}

if ($this->arrival) {
	$message .= '=== Arrival ==='."\n";
	$message .= 'Arrival Day: '. $this->arrival['day'] ."\n";
	$message .= 'Arrival Time: '. $this->arrival['time'] ."\n\n";
}
if ($this->departure) {
	$message .= '=== Departure ==='."\n";
	$message .= 'Departure Day: '. $this->departure['day'] ."\n";
	$message .= 'Departure Time: '. $this->departure['time'] ."\n\n";
}

/*if (!empty($this->bos)) {
	$message .= 'Break Out Session(s): '."\n";
	for ($i=0, $n=count( $this->bos ); $i < $n; $i++)
	{
		$message .= '  '.$this->bos[$i]."\n";
	}
	$message .= r.n;
}*/
if ($this->dinner) {
	$message .= '[x] Attending dinner.'."\n\n";
} else {
	$message .= '[ ] Attending dinner.'."\n\n";
}

if (isset($this->register['additional'])) {
	$message .= 'Additional: '. $this->register['additional'] ."\n\n";
}

if (isset($this->register['comments'])) {
	$message .= 'Comments: '. $this->register['comments'] ."\n\n";
}
echo $message;
