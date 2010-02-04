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

if ($modeventslatest->error) { ?>
	<p class="error"><?php echo $modeventslatest->error; ?></p>
<?php } else { ?>
<table class="latest_events_tbl" summary="<?php echo JText::_('MOD_EVENTS_LATEST_TABLE_SUMMARY'); ?>">
	<tbody>
<?php
if ($modeventslatest->eventsByRelDay) {
	$cls = 'even';
	foreach ($modeventslatest->eventsByRelDay as $relDay => $daysEvents)
	{
		reset($daysEvents);

		// Get all of the events for this day
		foreach ($daysEvents as $dayEvent)
		{
			// Get the title and start time
			$startDate = $dayEvent->publish_up;
			$eventDate = mktime(substr($startDate,11,2),substr($startDate,14,2), substr($startDate,17,2),date('m'),date('d') + $relDay,date('Y'));
			$startDate = mktime(substr($startDate,11,2),substr($startDate,14,2), substr($startDate,17,2), substr($startDate,5,2), substr($startDate,8,2), substr($startDate,0,4));
			$endDate = $dayEvent->publish_down;
			$endDate = mktime(substr($endDate,11,2),substr($endDate,14,2), substr($endDate,17,2), substr($endDate,5,2), substr($endDate,8,2), substr($endDate,0,4));

			$year = date('Y', $startDate);
			$month = date('m', $startDate);
			$day = date('d', $startDate);
			
			$cls = ($cls == 'even') ? 'odd' : 'even';
			
			if ($dayEvent->announcement == 1) {
				$cls .= ' announcement';
			}
?>
		<tr class="<?php echo $cls; ?>">
			<td class="event-date"><span class="month"><?php echo date('M',$eventDate); ?></span><span class="day"><?php echo date('d',$eventDate); ?></span></td>
			<td class="event-title"><a href="<?php echo JRoute::_('index.php?option=com_events&task=details&id='.$dayEvent->id); ?>"><?php echo $dayEvent->title; ?></a></td>
		</tr>
<?php 
		}
	}
} else {
?>
		<tr class="odd">
			<td class="mod_events_latest_noevents"><?php echo JText::_('MOD_EVENTS_LATEST_NONE_FOUND'); ?></td>
		</tr>
<?php
}
?>
	</tbody>
</table>
<p class="more"><a href="<?php echo JRoute::_('index.php?option=com_events&year='.strftime("%Y", time() ).'&month='.strftime("%m", time() )); ?>"><?php echo JText::_('MOD_EVENTS_LATEST_MORE'); ?></a></p>
<?php } ?>