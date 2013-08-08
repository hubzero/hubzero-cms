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
		<a class="icon-date btn date" title="" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&year='.$year.'&month='.$month); ?>">
			<?php echo JText::_('Back to Calendar'); ?>
		</a>
	</li>
</ul>

<div class="event-title-bar">
	<span class="event-title">
		<?php echo $this->event->title; ?>
	</span>
	<?php if ($this->juser->get('id') == $this->event->created_by || $this->authorized == 'manager') : ?>
		<a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=delete&event_id='.$this->event->id); ?>">
			Delete
		</a> 
		<a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=edit&event_id='.$this->event->id); ?>">
			Edit
		</a>
	<?php endif; ?>
</div>

<div class="event-sub-menu">
	<ul>
		<li>
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
		<?php endif; ?>
		
		<?php if ($this->juser->get('id') == $this->event->created_by || $this->authorized == 'manager') : ?>
			<li class="active">
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=registrants&event_id='.$this->event->id); ?>">
					<span><?php echo JText::_('Registrants ('.count($this->registrants).')'); ?></span>
				</a>
			</li>
		<?php endif; ?>
	</ul>
	<div class="clear"></div>
</div>

<table class="group-registrants">
	<thead>
		<tr>
			<th colspan="3">
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=download&event_id='.$this->event->id); ?>">Download Registrants (.csv)</a>
			</th>
		</tr>
		<tr>
			<th><?php echo JText::_('Name'); ?></th>
			<th><?php echo JText::_('Email'); ?></th>
			<th><?php echo JText::_('Register Date'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (count($this->registrants) > 0) : ?>
			<?php foreach ($this->registrants as $registrant) : ?>
				<tr>
					<td><?php echo $registrant->last_name . ', ' . $registrant->first_name; ?></td>
					<td><?php echo $registrant->email; ?></td>
					<td><?php echo date("l, F d, Y @ g:i a", strtotime($registrant->registered)); ?></td>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr>
				<td colspan="3">Currently there are no event registrants.</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>