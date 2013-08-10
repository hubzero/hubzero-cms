<?php
/**
 * @package     hubzero-cms
 * @author      Christopher Smoak <csmoak@purdue.edu>
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

$dateFormat = '%d %b, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$tz = false;
}

//get the database object
$database =& JFactory::getDBO();
?>


<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=sent'); ?>" method="post">
	<table class="data" summary="<?php echo JText::_('PLG_MEMBERS_MESSAGES_TBL_SUMMARY_OVERVIEW'); ?>">
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_MESSAGES_SUBJECT'); ?></th>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_MESSAGES_TO'); ?></th>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_MESSAGES_DATE_SENT'); ?></th>
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
			<?php if($this->rows) : ?>
				<?php foreach ($this->rows as $row) : ?>
					<?php
					
						//get the component that created message
						$component = (substr($row->component,0,4) == 'com_') ? substr($row->component,4) : $row->component;
					
						//url to view message
						$url = JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&msg='.$row->id);
					
						//get the message subject
						$subject = $row->subject;
						
						//support - special
						if ($component == 'support') {
							$fg = explode(' ',$row->subject);
							$fh = array_pop($fg);
							$subject = implode(' ',$fg);
						}
					
						//get the message
						$preview = ($row->message) ? "<h3>Message Preview:</h3>" . nl2br(stripslashes($row->message)) : "";
					
						//subject link
						$subject_cls = "message-link";
						
						$subject  = "<a class=\"{$subject_cls}\" href=\"{$url}\">{$subject}";
						//$subject .= "<div class=\"preview\"><span>" . $preview . "</span></div>";
						$subject .= "</a>";
					
						//get who the message is to
						$to = "<a href=\"" . JRoute::_('index.php?option='.$this->option.'&id='.$row->uid) . "\">" . $row->name . "</a>";
					
						//date received
						$date = JHTML::_('date', $row->created, $dateFormat, $tz);
					?>
				
					<tr>
						<td><?php echo $subject; ?></td>
						<td><?php echo $to; ?></td>
						<td><?php echo $date; ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="6"><?php echo JText::_('PLG_MEMBERS_MESSAGES_NONE'); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</form>
