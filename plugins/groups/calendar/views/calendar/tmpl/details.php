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

$year  = date("Y", strtotime($this->event->publish_up));
$month = date("m", strtotime($this->event->publish_up));
?>

<?php if($this->getError()) { ?>
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
		<?php echo $this->event->title; ?>
		<?php if (isset($this->calendar[0])) : ?>
			<span>&ndash;&nbsp;<?php echo $this->calendar[0]->title; ?></span>
		<?php endif; ?>
	</span>
	<?php if ($this->juser->get('id') == $this->event->created_by || $this->authorized == 'manager') : ?>
		<?php if (!isset($this->calendar[0]) || !$this->calendar[0]->readonly) : ?>
			<a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=delete&event_id='.$this->event->id); ?>">
				Delete
			</a> 
			<a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=edit&event_id='.$this->event->id); ?>">
				Edit
			</a>
		<?php endif; ?>
	<?php endif; ?>
</div>

<div class="event-sub-menu">
	<ul>
		<li class="active">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=details&event_id='.$this->event->id); ?>">
				<span><?php echo JText::_('Details'); ?></span>
			</a>
		</li>
		
		<?php if (isset($this->event->registerby) && $this->event->registerby != '' && $this->event->registerby != '0000-00-00 00:00:00') : ?>
			<li>
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=register&event_id='.$this->event->id); ?>">
					<span><?php echo JText::_('Register'); ?></span>
				</a>
			</li>
			<?php if ($this->juser->get('id') == $this->event->created_by || $this->authorized == 'manager') : ?>
				<li>
					<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=registrants&event_id='.$this->event->id); ?>">
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
		<?php if ($this->event->publish_down != '0000-00-00 00:00:00') : ?>
			<tr>
				<th class="date"></th>
				<td colspan="3">
					<?php $tz = Hubzero_Event_Helper::getTimezoneNameAndAbbreviation( $this->event->time_zone ); ?>
					<?php echo date("l, F d, Y @ g:i a", strtotime($this->event->publish_up)); ?>
					<abbr title="<?php echo $tz['name']; ?>"><?php echo $tz['abbreviation']; ?></abbr>
					&mdash;
					<?php echo date("l, F d, Y @ g:i a", strtotime($this->event->publish_down)); ?>
					<abbr title="<?php echo $tz['name']; ?>"><?php echo $tz['abbreviation']; ?></abbr>
				</td>
			</tr>
		<?php else : ?>
			<tr>
				<th class="date"></th>
				<td width="50%"><?php echo date("l, F d, Y", strtotime($this->event->publish_up)); ?></td>
				<th class="time"></th>
				<td>
					<?php echo date("g:i a", strtotime($this->event->publish_up)); ?>
					<?php $tz = Hubzero_Event_Helper::getTimezoneNameAndAbbreviation( $this->event->time_zone ); ?>
					<abbr title="<?php echo $tz['name']; ?>"><?php echo $tz['abbreviation']; ?></abbr>
				</td>
			</tr>
		<?php endif; ?>
		
		<?php if (isset($this->event->adresse_info) && $this->event->adresse_info != '') : ?>
			<tr>
				<th class="location"></th>
				<td colspan="3"><?php echo $this->event->adresse_info; ?></td>
			</tr>
		<?php endif; ?>
		
		<?php if (isset($this->event->contact_info) && $this->event->contact_info != '') : ?>
			<tr>
				<th class="author"></th>
				<td colspan="3"><?php echo Hubzero_Event_Helper::autoLinkText( $this->event->contact_info ); ?></td>
			</tr>
		<?php endif; ?>
		
		<?php if (isset($this->event->extra_info) && $this->event->extra_info != '') : ?>
			<tr>
				<th class="url"></th>
				<td colspan="3"><?php echo Hubzero_Event_Helper::autoLinkText( $this->event->extra_info ); ?></td>
			</tr>
		<?php endif; ?>
		
		<?php if (isset($this->event->content) && $this->event->content != '') : ?>
			<tr>
				<th class="details"></th>
				<td colspan="3"><?php echo nl2br($this->event->content); ?></td>
			</tr>
		<?php endif; ?>
		
		<tr>
			<td colspan="4"></td>
		</tr>
		<tr>
			<th class="download"></th>
			<td colspan="4">
				<a class="btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=export&event_id='.$this->event->id); ?>"><?php echo JText::_('Export to My Calendar (ics)'); ?></a>
			</td>
		</tr>
	</tbody>
</table>