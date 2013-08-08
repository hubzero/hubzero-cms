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
?>

<?php if($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
	<li>
		<a class="icon-prev btn back" title="" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&year='.$this->year.'&month='.$this->month); ?>">
			<?php echo JText::_('Back to Events Calendar'); ?>
		</a>
		<a class="icon-add btn add" title="" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=addcalendar'); ?>">
			<?php echo JText::_('Add Calendar'); ?>
		</a>
		
	</li>
</ul>
<!--
<div class="event-title-bar">
	<span class="event-title">
		<?php echo JText::_('Group Calendars'); ?>
	</span>
</div>
-->
<table class="group-calendars">
	<thead>
		<tr>
			<th><?php echo JText::_('Name'); ?></th>
			<th><?php echo JText::_('Color'); ?></th>
			<th><?php echo JText::_('Publish Events to Subscribers?'); ?></th>
			<th><?php echo JText::_('Actions'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (count($this->calendars) > 0) : ?>
			<?php foreach ($this->calendars as $calendar) : ?>
				<tr>
					<td><?php echo $calendar->title; ?></td>
					<td>
						<?php if ($calendar->color): ?>
							<img src="/plugins/groups/calendar/images/swatch-<?php echo $calendar->color; ?>.png" />
						<?php else: ?>
							<img src="/plugins/groups/calendar/images/swatch-gray.png" />
						<?php endif; ?>
					</td>
					<td>
						<?php
							if ($calendar->published == 1)
							{
								echo '<span class="yes">Yes</span>';
							}
							else
							{
								echo '<span class="no">No</span>';
							}
						?>
					</td>
					<td>
						<a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=editcalendar&calendar_id=' . $calendar->id); ?>">
							Edit
						</a> &nbsp;|
						<a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=deletecalendar&calendar_id=' . $calendar->id); ?>">
							Delete
						</a>
						<?php if ($calendar->url) : ?>
							 &nbsp;|
							<a class="refresh" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=refreshcalendar&calendar_id=' . $calendar->id); ?>">
								Refresh
							</a>
						<?php endif; ?>	
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<?php if ($calendar->url) : ?>
							<span class="calendar-url">
								<span>Calendar URL:</span>
								<?php echo $calendar->url; ?>
							</span>
							<br />
							<span class="calendar-url">
								<span>Last Fetched:</span>
								<?php 
									if ($calendar->last_fetched == '' || $calendar->last_fetched == '0000-00-00 00:00:00')
									{
										echo 'Never';
									}
									else
									{
										echo date('m/d/Y @ g:ia', strtotime($calendar->last_fetched));	
									}
								?>
							</span>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr>
				<td colspan="4">Currently there are no calendars for this group.</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>