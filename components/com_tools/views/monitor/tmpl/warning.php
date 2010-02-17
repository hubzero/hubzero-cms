<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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