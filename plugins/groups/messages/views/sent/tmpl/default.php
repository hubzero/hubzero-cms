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
<h3><a name="messages"></a><?php echo JText::_('MESSAGES'); ?></h3>
<div class="withleft">
	<div class="aside">
		<ul>
			<li class="active"><a class="sent" href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=messages'); ?>"><span><?php echo JText::_('PLG_GROUPS_MESSAGES_SENT'); ?></span></a></li>
			<li><a class="new-message" href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=messages&task=new'); ?>"><span><?php echo JText::_('PLG_GROUPS_MESSAGES_SEND'); ?></span></a></li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
		<form action="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->group->get('cn').'&active=messages'); ?>" method="post" id="hubForm">
			<table class="data" summary="<?php echo JText::_('TBL_SUMMARY_OVERVIEW'); ?>">
				<thead>
					<tr>
						<th scope="col"><?php echo JText::_('Subject'); ?></th>
						<th scope="col"><?php echo JText::_('From'); ?></th>
						<th scope="col"><?php echo JText::_('Date Sent'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="3">
<?php 
			$pagenavhtml = $this->pageNav->getListFooter();
			$pagenavhtml = str_replace('groups/?','groups/'.$this->group->get('cn').'/messages/sent/?',$pagenavhtml);
			$pagenavhtml = str_replace('action=sent','',$pagenavhtml);
			$pagenavhtml = str_replace('&amp;&amp;','&amp;',$pagenavhtml);
			$pagenavhtml = str_replace('?&amp;','?',$pagenavhtml);
			echo $pagenavhtml;
?>
						</td>
					</tr>
				</tfoot>
				<tbody>
<?php
if ($this->rows) {
	$cls = 'even';
	foreach ($this->rows as $row) 
	{
		$cls = (($cls == 'even') ? 'odd' : 'even');
		//$u =& JUser::getInstance($row->created_by);
?>
					<tr class="<?php echo $cls; ?>">
						<td><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=messages&msg='.$row->id); ?>"><?php echo stripslashes($row->subject); ?></a></td>
						<td><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by); ?>"><?php echo stripslashes($row->name); ?></a></td>
						<td><?php echo JHTML::_('date', $row->created, '%d %b, %Y'); ?></td>
					</tr>
<?php
	}
} else { ?>
					<tr class="odd">
						<td colspan="3"><?php echo JText::_('No messages found'); ?></td>
					</tr>
<?php } ?>
				</tbody>
			</table>
		</form>
	</div><!-- / .subject -->
</div>