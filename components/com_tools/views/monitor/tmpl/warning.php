<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$hms = '';

$days = intval(intval($this->sec) / 86400);
if ($days) {
	$hms .= $days.' days, ';
	$this->sec = intval($this->sec) - 86400;
}
// there are 3600 seconds in an hour, so if we
// divide total seconds by 3600 and throw away
// the remainder, we've got the number of hours
$hours = intval(intval($this->sec) / 3600);

// add to $hms, with a leading 0 if asked for
if ($hours) {
	$hms .= ($this->padHours) 
			? str_pad($hours, 2, "0", STR_PAD_LEFT). ' hours,'
			: $hours. ' hours, ';
}

// dividing the total seconds by 60 will give us
// the number of minutes, but we're interested in
// minutes past the hour: to get that, we need to
// divide by 60 again and keep the remainder
$minutes = intval(($this->sec / 60) % 60);

if ($minutes) {
	// then add to $hms (with a leading 0 if needed)
	$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ' minutes, ';
}

// seconds are simple - just divide the total
// seconds by 60 and keep the remainder
$this->seconds = intval($this->sec % 60);

// add to $hms, again with a leading 0 if needed
$hms .= str_pad($this->seconds, 2, "0", STR_PAD_LEFT).' seconds';

$msg  = 'You are currently at or exceeding your storage limit. ';
$msg .= $hms.' remain until you can no longer store more data. ';
$msg .= '<a href="'.JRoute::_('index.php?option='.$this->option.'&task=storageexceeded').'">Learn more on how to resolve this</a>.';
echo '<p aclass="warning">'.$msg.'</p>';
?>
