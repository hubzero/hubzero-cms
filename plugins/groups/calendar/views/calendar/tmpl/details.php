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

$year  = date("Y", strtotime($this->event->get('publish_up')));
$month = date("m", strtotime($this->event->get('publish_up')));
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
	<li>
		<a class="icon-prev btn back" title="" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&year='.$year.'&month='.$month); ?>">
			<?php echo JText::_('Back to Events Calendar'); ?>
		</a>
	</li>
</ul>

<div class="event-title-bar">
	<span class="event-title">
		<?php echo $this->event->get('title'); ?>
		<?php if (isset($this->calendar)) : ?>
			<span>&ndash;&nbsp;<?php echo $this->calendar->get('title'); ?></span>
		<?php endif; ?>
	</span>
	<?php if ($this->juser->get('id') == $this->event->get('created_by') || $this->authorized == 'manager') : ?>
		<?php if (!isset($this->calendar) || !$this->calendar->get('readonly')) : ?>
			<a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=delete&event_id='.$this->event->get('id')); ?>">
				Delete
			</a>
			<a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=edit&event_id='.$this->event->get('id')); ?>">
				Edit
			</a>
		<?php endif; ?>
	<?php endif; ?>
</div>

<div class="event-sub-menu">
	<ul>
		<li class="active">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=details&event_id='.$this->event->get('id')); ?>">
				<span><?php echo JText::_('Details'); ?></span>
			</a>
		</li>

		<?php if ($this->event->get('registerby') != '' && $this->event->get('registerby') != '0000-00-00 00:00:00') : ?>
			<li>
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=register&event_id='.$this->event->get('id')); ?>">
					<span><?php echo JText::_('Register'); ?></span>
				</a>
			</li>
			<?php if ($this->juser->get('id') == $this->event->get('created_by') || $this->authorized == 'manager') : ?>
				<li>
					<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=registrants&event_id='.$this->event->get('id')); ?>">
						<span><?php echo JText::_('Registrants ('.$this->registrants.')'); ?></span>
					</a>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	</ul>
	<div class="clear"></div>
</div>

<table class="group-event-details">
	<tbody>
		<?php
			$timezone     = timezone_name_from_abbr('',$this->event->get('time_zone')*3600, NULL);
			$publish_up   = $this->event->get('publish_up');
			$publish_down = $this->event->get('publish_down');
			$allday_event = $this->event->get('allday');

			// show alternative event start/ends
			// used for repeating events
			$start = JRequest::getInt('start', NULL, 'get');
			$end   = JRequest::getInt('end', NULL, 'get');
			if ($start || ($start && $end))
			{
				$publish_up   = JFactory::getDate($start)->toSql();
				$publish_down = JFactory::getDate($end)->toSql();
			}
		?>
		<?php if ($allday_event) : ?>
			<tr>
				<th class="date"></th>
				<td width="50%">
					<?php echo JHTML::_('date', $publish_up, 'l, F d, Y'); ?>
				</td>
				<th class="time"></th>
				<td>
					<?php echo JText::_('All Day Event'); ?>
				</td>
			</tr>
		<?php elseif ($publish_down != '0000-00-00 00:00:00') : ?>
			<tr>
				<th class="date"></th>
				<td colspan="3">
					<?php echo JHTML::_('date', $publish_up, 'l, F d, Y @ g:i a') . JHTML::_('date', $publish_up, ' T', $timezone); ?>
					&mdash;
					<?php echo JHTML::_('date', $publish_down, 'l, F d, Y @ g:i a') . JHTML::_('date', $publish_down, ' T', $timezone); ?>
				</td>
			</tr>
		<?php else : ?>
			<tr>
				<th class="date"></th>
				<td width="50%">
					<?php echo JHTML::_('date', $publish_up, 'l, F d, Y'); ?>
				</td>
				<th class="time"></th>
				<td>
					<?php echo JHTML::_('date', $publish_up, 'g:i a') . JHTML::_('date', $publish_up, ' T', $timezone); ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ($this->event->get('repeating_rule') != '') : ?>
			<tr>
				<th class="repeatig"></th>
				<td colspan="3"><?php echo $this->event->humanReadableRepeatingRule(); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ($this->event->get('adresse_info') != '') : ?>
			<tr>
				<th class="location"></th>
				<td colspan="3"><?php echo $this->event->get('adresse_info'); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ($this->event->get('contact_info') != '') : ?>
			<tr>
				<th class="author"></th>
				<td colspan="3"><?php echo plgGroupsCalendarHelper::autoLinkText( $this->event->get('contact_info') ); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ($this->event->get('extra_info') != '') : ?>
			<tr>
				<th class="url"></th>
				<td colspan="3">
					<a href="<?php echo $this->event->get('extra_info'); ?>" rel="external">
						<?php echo $this->event->get('extra_info'); ?>
					</a>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ($this->event->get('content') != '') : ?>
			<tr>
				<th class="details"></th>
				<td colspan="3"><?php echo plgGroupsCalendarHelper::autoLinkText(nl2br($this->event->get('content'))); ?></td>
			</tr>
		<?php endif; ?>

		<tr>
			<td colspan="4"></td>
		</tr>
		<tr>
			<th class="download"></th>
			<td colspan="4">
				<a class="btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=export&event_id='.$this->event->get('id')); ?>"><?php echo JText::_('Export to My Calendar (ics)'); ?></a>
			</td>
		</tr>
	</tbody>
</table>