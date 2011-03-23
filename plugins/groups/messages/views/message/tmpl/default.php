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

if (substr($this->xmessage->type, -8) == '_message') {
	$u =& JUser::getInstance($this->xmessage->created_by);
	$from = '<a href="'.JRoute::_('index.php?option='.$option.'&id='.$u->get('id')).'">'.$u->get('name').'</a>';
} else {
	$from = 'System ('.$this->xmessage->component.')';
}

?>
<a name="messages"></a>
<h3><?php echo JText::_('MESSAGES'); ?></h3>

<div class="subject">
	<ul class="entries-menu">
		<li><a class="active" href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=messages'); ?>"><span><?php echo JText::_('PLG_GROUPS_MESSAGES_SENT'); ?></span></a></li>
		<?php if($this->authorized == 'admin' || $this->authorized == 'manager') { ?>
			<li><a href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=messages&task=new'); ?>"><span><?php echo JText::_('PLG_GROUPS_MESSAGES_SEND'); ?></span></a></li>
		<?php } ?>
	</ul>
	<div class="container">
		<form action="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=messages'); ?>" method="post">
		<table class="groups entries" summary="Groups this person is a member of">
			<caption><?php echo JText::_('PLG_GROUPS_MESSAGE'); ?> <span><small>( <a href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=messages'); ?>">&lsaquo; Back to Sent Messages</a> )</small></span></caption>
			<tbody>
				<tr>
					<th><?php echo JText::_('PLG_GROUPS_MESSAGES_RECEIVED'); ?>:</th>
					<td><?php echo JHTML::_('date', $this->xmessage->created, '%d %b, %Y'); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('PLG_GROUPS_MESSAGES_FROM'); ?>:</th>
					<td><?php echo $from; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('PLG_GROUPS_MESSAGES_SUBJECT'); ?>:</th>
					<td><?php echo stripslashes($this->xmessage->subject); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('PLG_GROUPS_MESSAGES_MESSAGE'); ?>:</th>
					<td><?php echo $this->xmessage->message; ?></td>
				</tr>
			</tbody>
		</table>
		</form>
	</div>
</div><!-- // .subject -->

