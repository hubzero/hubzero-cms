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

<?php if ($this->getError()) { ?>
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
			<th><?php echo JText::_('Publish Events?'); ?></th>
			<th><?php echo JText::_('Actions'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (count($this->calendars) > 0) : ?>
			<?php foreach ($this->calendars as $calendar) : ?>
				<tr>
					<td><?php echo $calendar->get('title'); ?></td>
					<td>
						<?php if ($calendar->get('color')): ?>
							<img src="/plugins/groups/calendar/images/swatch-<?php echo $calendar->get('color'); ?>.png" />
						<?php else: ?>
							<img src="/plugins/groups/calendar/images/swatch-gray.png" />
						<?php endif; ?>
					</td>
					<td>
						<?php
							if ($calendar->get('published') == 1)
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
						<a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=editcalendar&calendar_id=' . $calendar->get('id')); ?>">
							Edit
						</a> &nbsp;|
						<a class="delete" href="javascript:void(0);">
							Delete
						</a>
						<?php if ($calendar->get('url')) : ?>
							 &nbsp;|
							<a class="refresh" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=refreshcalendar&calendar_id=' . $calendar->get('id')); ?>">
								Refresh
							</a>
						<?php endif; ?>
					</td>
				</tr>
				<tr class="delete-confirm">
					<td colspan="4">
						<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=deletecalendar&calendar_id=' . $calendar->get('id')); ?>" method="post">
							<h3>Delete Calendar</h3>
							<p>What do you want to do with the events associated with this calendar?</p>
							<select name="events">
								<option value="keep">Delete Calendar &amp; Set Events as Uncategorized</option>
								<option value="delete">Delete Calendar &amp; Delete Events</option>
							</select>
							<input class="btn btn-danger" type="submit" value="Delete" />
							<input class="btn btn-secondary delete-cancel" type="reset" value="Cancel" />
						</form>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<?php if ($calendar->get('url')) : ?>
							<span class="calendar-url">
								<span>Calendar URL:</span>
								<?php echo $calendar->get('url'); ?>
							</span>
							<br />
							<span class="calendar-url">
								<span>Last Fetched:</span>
								<?php
									if ($calendar->get('last_fetched') == '' || $calendar->get('last_fetched') == '0000-00-00 00:00:00')
									{
										echo 'Never';
									}
									else
									{
										echo JHTML::_('date', $calendar->get('last_fetched'), 'm/d/Y @ g:ia');
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