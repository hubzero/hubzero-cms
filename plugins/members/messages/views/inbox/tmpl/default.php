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
<h3><a name="messages"></a><?php echo JText::_('PLG_MEMBERS_MESSAGES'); ?></h3>
<div class="withleft">
	<div class="aside">
		<ul>
			<li class="active"><a class="box" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=inbox'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_INBOX'); ?></span></a></li>
			<li><a class="sent" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=sent'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_SENT'); ?></span></a></li>
			<li><a class="archive" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=archive'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_ARCHIVE'); ?></span></a></li>
			<li><a class="trash" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=trash'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_TRASH'); ?></span></a></li>
			<li><a class="config" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=settings'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_SETTINGS'); ?></span></a></li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages'); ?>" method="post" id="hubForm" class="full">
			<fieldset id="filters">
				<input type="hidden" name="inaction" value="inbox" />
				<?php echo JText::_('PLG_MEMBERS_MESSAGES_FROM'); ?> 
				<select class="option" name="filter">
					<option value=""><?php echo JText::_('PLG_MEMBERS_MESSAGES_ALL'); ?></option>
<?php
			if ($this->components) {
				foreach ($this->components as $component) 
				{
					$component = substr($component, 4);
					$sbjt  = "\t\t\t".'<option value="'.$component.'"';
					$sbjt .= ($component == $this->filter) ? ' selected="selected"' : '';
					$sbjt .= '>'.$component.'</option>'."\n";
					echo $sbjt;
				}
			}
?>
				</select> 
				<input class="option" type="submit" value="<?php echo JText::_('PLG_MEMBERS_MESSAGES_FILTER'); ?>" />
			</fieldset>
			<fieldset id="actions">
				<select class="option" name="action">
					<option value=""><?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_WITH_SELECTED'); ?></option>
					<option value="markasread"><?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_MARK_AS_READ'); ?></option>
					<option value="sendtoarchive"><?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_SEND_TO_ARCHIVE'); ?></option>
					<option value="sendtotrash"><?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_SEND_TO_TRASH'); ?></option>
				</select> 
				<input class="option" type="submit" value="<?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_APPLY'); ?>" />
			</fieldset>
			<table class="data" summary="<?php echo JText::_('PLG_MEMBERS_MESSAGES_TBL_SUMMARY_OVERVIEW'); ?>">
				<thead>
					<tr>
						<th scope="col"><input type="checkbox" name="msgall" id="msgall" value="all"  onclick="HUB.MembersMsg.checkAll(this, 'chkbox');" /></th>
						<th scope="col"> </th>
						<th scope="col"><?php echo JText::_('PLG_MEMBERS_MESSAGES_SUBJECT'); ?></th>
						<th scope="col"><?php echo JText::_('PLG_MEMBERS_MESSAGES_FROM'); ?></th>
						<th scope="col"><?php echo JText::_('PLG_MEMBERS_MESSAGES_DATE_RECEIVED'); ?></th>
						<th scope="col"> </th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6">
							<?php echo $this->pagenavhtml; ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
<?php
if ($this->rows) {
	$database =& JFactory::getDBO();
	$cls = 'even';
	foreach ($this->rows as $row) 
	{
		if ($row->whenseen != '' && $row->whenseen != '0000-00-00 00:00:00') {
			$status = '<span class="read status"></span>';
			$lnkcls = '';
		} else {
			$status = '<span class="unread status">*</span>';
			$lnkcls = 'class="unread" ';
		}
		
		if (substr($row->component,0,4) == 'com_') {
			$row->component = substr($row->component,4);
		}

		if ($row->component == 'support') {
			$fg = explode(' ',$row->subject);
			$fh = array_pop($fg);
			$row->subject = implode(' ',$fg);
		}
		
		$url = JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&msg='.$row->id);
		
		$cls = (($cls == 'even') ? 'odd' : 'even');
		if ($row->actionid) {
			$xma = new Hubzero_Message_Action( $database );
			$xma->load( $row->actionid );
			if ($xma) {
				$url = JRoute::_(stripslashes($xma->description));
			}
		}
		
		if ($row->actionid) {
			$cls .= ' actionitem';
		}
?>
					<tr class="<?php echo $cls; ?>">
<?php if ($row->actionid && ($row->whenseen == '' || $row->whenseen == '0000-00-00 00:00:00')) { ?>
						<td class="check"> </td>
<?php } else { ?>
						<td class="check"><input class="chkbox" type="checkbox" name="mid[]" id="msg<?php echo $row->id; ?>" value="<?php echo $row->id; ?>" /></td>
<?php } ?>
						<td class="sttus"><?php echo $status; ?></td>
						<td><a <?php echo $lnkcls; ?>href="<?php echo $url; ?>"><?php echo stripslashes($row->subject); ?></a></td>
<?php
if (substr($row->type, -8) == '_message') {
	$u =& JUser::getInstance($row->created_by);
?>
						<td><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$u->get('id')); ?>"><?php echo $u->get('name'); ?></a></td>
<?php } else { ?>
						<td><?php echo JText::sprintf('PLG_MEMBERS_MESSAGES_SYSTEM', $row->component); ?></td>
<?php } ?>
						<td><?php echo JHTML::_('date', $row->created, '%d %b, %Y'); ?></td>
<?php if ($row->actionid && ($row->whenseen == '' || $row->whenseen == '0000-00-00 00:00:00')) { ?>
						<td> </td>
<?php } else { ?>
						<td><a class="trash" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&mid[]='.$row->id.'&action=sendtotrash'); ?>" title="<?php echo JText::_('PLG_MEMBERS_MESSAGES_TRASH'); ?>"><?php echo JText::_('PLG_MEMBERS_MESSAGES_TRASH'); ?></a></td>
<?php } ?>
					</tr>
<?php
	}
} else { ?>
					<tr class="odd">
						<td colspan="6"><?php echo JText::_('PLG_MEMBERS_MESSAGES_NONE'); ?></td>
					</tr>
<?php } ?>
				</tbody>
			</table>
		</form>
	</div><!-- / .subject -->
</div><!-- / .withleft -->