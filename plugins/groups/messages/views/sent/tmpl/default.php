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
	<br class="clear" />
	<div class="container">
		<form action="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=messages'); ?>" method="post">
		<table class="groups entries" summary="Groups this person is a member of">
			<caption><?php echo JText::_('PLG_GROUPS_MESSAGES_SENT'); ?> <span>(<?php echo count($this->rows); ?>)</span></caption>
			<thead>
				<tr>
					<th scope="col"><?php echo JText::_('Subject'); ?></th>
					<th scope="col"><?php echo JText::_('Message From'); ?></th>
					<th scope="col"><?php echo JText::_('Date Sent'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if(count($this->rows) > 0) { ?>
					<?php foreach($this->rows as $row) { ?>
						<tr>
							<td><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=messages&task=viewmessage&msg='.$row->id); ?>"><?php echo stripslashes($row->subject); ?></a></td>
							<td><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by); ?>"><?php echo stripslashes($row->name); ?></a></td>
							<td><?php echo JHTML::_('date', $row->created, '%d %b, %Y'); ?></td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="3"><?php echo JText::_('No messages found'); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		</form>
	</div>
	
	<?php 
		$pagenavhtml = $this->pageNav->getListFooter();
		$pagenavhtml = str_replace('groups/?','groups/'.$this->group->get('cn').'/messages/sent/?',$pagenavhtml);
		$pagenavhtml = str_replace('action=sent','',$pagenavhtml);
		$pagenavhtml = str_replace('&amp;&amp;','&amp;',$pagenavhtml);
		$pagenavhtml = str_replace('?&amp;','?',$pagenavhtml);
		echo $pagenavhtml;
	?>
	
</div><!-- // .subject -->

