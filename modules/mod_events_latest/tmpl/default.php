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

if ($this->setError())
{
?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php 
}
else
{
?>
<table class="latest_events_tbl">
	<tbody>
<?php
if ($this->eventsByRelDay)
{
	$cls = 'even';
	foreach ($this->eventsByRelDay as $relDay => $daysEvents)
	{
		reset($daysEvents);

		// Get all of the events for this day
		foreach ($daysEvents as $dayEvent)
		{
			// Get the title and start time
			$startDate = $dayEvent->publish_up;
			$eventDate = mktime(substr($startDate, 11, 2), substr($startDate, 14, 2), substr($startDate, 17, 2), date('m'), date('d') + $relDay, date('Y'));
			$startDate = mktime(substr($startDate, 11, 2), substr($startDate, 14, 2), substr($startDate, 17, 2), substr($startDate, 5, 2), substr($startDate, 8, 2), substr($startDate, 0, 4));
			$endDate = $dayEvent->publish_down;
			$endDate = mktime(substr($endDate, 11, 2), substr($endDate, 14, 2), substr($endDate, 17, 2), substr($endDate, 5, 2), substr($endDate, 8, 2), substr($endDate, 0, 4));

			$year  = date('Y', $startDate);
			$month = date('m', $startDate);
			$day   = date('d', $startDate);

			$cls = ($cls == 'even') ? 'odd' : 'even';

			if ($dayEvent->announcement == 1)
			{
				$cls .= ' announcement';
			}
?>
		<tr class="<?php echo $cls; ?>">
			<td class="event-date"><span class="month"><?php echo date('M', $eventDate); ?></span><span class="day"><?php echo date('d', $eventDate); ?></span></td>
			<td class="event-title"><a href="<?php echo JRoute::_('index.php?option=com_events&task=details&id=' . $dayEvent->id); ?>"><?php echo stripslashes($dayEvent->title); ?></a></td>
		</tr>
<?php 
		}
	}
}
else
{
?>
		<tr class="odd">
			<td class="mod_events_latest_noevents"><?php echo JText::_('MOD_EVENTS_LATEST_NONE_FOUND'); ?></td>
		</tr>
<?php
}
?>
	</tbody>
</table>
<p class="more"><a href="<?php echo JRoute::_('index.php?option=com_events&year=' . strftime("%Y", time()) . '&month=' . strftime("%m", time())); ?>"><?php echo JText::_('MOD_EVENTS_LATEST_MORE'); ?></a></p>
<?php 
}
?>