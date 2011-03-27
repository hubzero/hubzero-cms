<?php
/**
 * @package     hubzero-cms
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
JToolBarHelper::title( '<a href="index.php?option=com_events">'.JText::_( 'EVENTS' ).'</a>: <small><small>[ '.JText::_('RESPONDANT').' ]</small></small>', 'user.png' );
//JToolBarHelper::cancel();

$resp = $this->resp;

list($resp) = $resp->getRecords();
?>
<h2><?php echo stripslashes($this->event->title); ?></h2>

<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
	<thead>
		<tr><th colspan="2"><?php echo JText::_('RESPONDENT_DATA'); ?></th></tr>
	</thead>
	<tbody>
		<?php if (!empty($resp->last_name) || !empty($resp->first_name)) : ?>
		<tr><td><?php echo JText::_('NAME'); ?></td><td><?php echo $resp->last_name . ', ' . $resp->first_name; ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->email)): ?>
		<tr><td><?php echo JText::_('EMAIL'); ?></td><td><a href="mailto:<?php echo $resp->email; ?>"><?php echo $resp->email; ?></a></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->affiliation)): ?>
		<tr><td><?php echo JText::_('AFFILIATION'); ?></td><td><?php echo $resp->affiliation; ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->title)): ?>
		<tr><td><?php echo JText::_('TITLE'); ?></td><td><?php echo $resp->title . (empty($resp->position_description) ? '' : ' - ' . $resp->position_description); ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->city) || !empty($resp->state) || !empty($resp->zip) || !empty($resp->country)): ?>
		<tr><td><?php echo JText::_('LOCATION'); ?></td><td><?php echo $resp->city . ' ' . $resp->state . ' ' . $resp->country . ' ' . $resp->zip; ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->telephone) || !empty($resp->fax)): ?>
		<tr><td><?php echo JText::_('TELEPHONE'); ?></td><td><?php echo $resp->telephone . (empty($resp->fax) ? '' : ' ' . $resp->fax . ' ('.JText::_('FAX').')'); ?></td></tr>
		<?php endif; ?>	
		<?php if (!empty($resp->website)): ?>
		<tr><td><?php echo JText::_('WEBSITE'); ?></td><td><?php echo $resp->website; ?></td></tr>
		<?php endif; ?>
		<?php 
		$race = EventsRespondent::getRacialIdentification($resp->id);
		if (!empty($race)): 
		?>
		<tr><td><?php echo JText::_('RACE'); ?></td><td><?php echo $race; ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->gender)): ?>
		<tr><td><?php echo JText::_('GENDER'); ?></td><td><?php echo $resp->gender == 'm' ? JText::_('MALE') : JText::_('FEMALE'); ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->arrival)): ?>
		<tr><td><?php echo JText::_('ARRIVAL'); ?></td><td><?php echo $resp->arrival; ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->arrival)): ?>
		<tr><td><?php echo JText::_('DEPARTURE'); ?></td><td><?php echo $resp->departure; ?></td></tr>
		<?php endif; ?>
		<tr><td><?php echo JText::_('DISABILITY_CONTACT_REQUESTED'); ?></td><td><?php echo $resp->disability_needs ? JText::_('YES') : JText::_('NO'); ?></td></tr>
		<?php if (!empty($resp->dietary_needs)): ?>
		<tr><td><?php echo JText::_('DIETARY_RESTRICTION'); ?></td><td><?php echo $resp->dietary_needs; ?></td></tr>
		<?php endif; ?>
		<tr><td><?php echo JText::_('ATTENDING_DINNER'); ?></td><td><?php echo $resp->attending_dinner ? JText::_('YES') : JText::_('NO'); ?></td></tr>
		<?php if (!empty($resp->abstract)): ?>
		<tr><td><?php echo JText::_('ABSTRACT'); ?></td><td><?php echo $resp->abstract; ?></td></tr>
		<?php endif; ?>
		<?php if (!empty($resp->comment)): ?>
		<tr><td><?php echo JText::_('COMMENT'); ?></td><td><?php echo $resp->comment; ?></td></tr>
		<?php endif; ?>
	</tbody>
</table>
