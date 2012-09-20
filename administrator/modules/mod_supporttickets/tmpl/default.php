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

$app = JFactory::getApplication();
?>
<div id="mod_supporttickets">
<?php if ($app->getTemplate() == 'khepri' && $this->module->showtitle) : ?>
	<h3 class="title"><?php echo $this->module->title; ?></h3>
<?php endif; ?>
	<table class="support-stats-overview open-tickets" summary="<?php echo JText::_('Overview of open support tickets'); ?>">
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('Open'); ?></th>
				<th scope="col"><?php echo JText::_('Unassigned'); ?></th>
				<th scope="col"><?php echo JText::_('New'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="major"><a href="index.php?option=com_support&amp;controller=tickets&amp;show=<?php echo $this->opened[0]->id; ?>" title="<?php echo JText::_('View open tickets'); ?>"><?php echo $this->opened[0]->count; ?></a></td>
				<td class="critical"><a href="index.php?option=com_support&amp;controller=tickets&amp;show=<?php echo $this->opened[2]->id; ?>" title="<?php echo JText::_('View unassigned tickets'); ?>"><?php echo $this->opened[2]->count; ?></a></td>
				<td class="newt"><a href="index.php?option=com_support&amp;controller=tickets&amp;show=<?php echo $this->opened[1]->id; ?>" title="<?php echo JText::_('View new tickets'); ?>"><?php echo $this->opened[1]->count; ?></a></td>
			</tr>
		</tbody>
	</table>

	<table class="support-stats-overview closed-tickets" summary="<?php echo JText::_('Overview of closed support tickets'); ?>">
		<thead>
			<tr>
				<th scope="col" class="block"><?php echo JText::_('Average lifetime'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="block">
					<?php echo (isset($this->lifetime[0])) ? $this->lifetime[0] : 0; ?> <span><?php echo JText::_('days'); ?></span> 
					<?php echo (isset($this->lifetime[1])) ? $this->lifetime[1] : 0; ?> <span><?php echo JText::_('hours'); ?></span> 
					<?php echo (isset($this->lifetime[2])) ? $this->lifetime[2] : 0; ?> <span><?php echo JText::_('minutes'); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
<?php if ($this->params->get('showMine', 1)) { ?>
	<table class="support-stats-overview my-tickets" summary="<?php echo JText::_('Overview of my support tickets'); ?>">
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('My Tickets (reported)'); ?></th>
				<th scope="col"><?php echo JText::_('My Tickets (assigned)'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><a href="index.php?option=com_support&amp;controller=tickets&amp;show=<?php echo $this->my[0]->id; ?>" title="<?php echo JText::_('View my reported tickets'); ?>"><?php echo $this->my[0]->count; ?></a></td>
				<td><a href="index.php?option=com_support&amp;controller=tickets&amp;show=<?php echo $this->my[1]->id; ?>" title="<?php echo JText::_('View my assigned tickets'); ?>"><?php echo $this->my[1]->count; ?></a></td>
			</tr>
		</tbody>
	</table>
<?php } ?>
</div>