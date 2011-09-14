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
?>
<h3 class="section-header"><a name="messages"></a><?php echo JText::_('PLG_MEMBERS_MESSAGES'); ?></h3>
<div class="withleft">
	<div class="aside">
		<ul>
			<li><a class="box" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=inbox'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_INBOX'); ?></span></a></li>
			<li class="active"><a class="sent" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=sent'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_SENT'); ?></span></a></li>
			<li><a class="archive" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=archive'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_ARCHIVE'); ?></span></a></li>
			<li><a class="trash" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=trash'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_TRASH'); ?></span></a></li>
			<li><a class="config" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=settings'); ?>"><span><?php echo JText::_('PLG_MEMBERS_MESSAGES_SETTINGS'); ?></span></a></li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&task=sent'); ?>" method="post" id="hubForm" class="full">
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
						<td colspan="3">
							<?php echo $this->pagenavhtml; ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
<?php
if ($this->rows) {
	$cls = 'even';
	foreach ($this->rows as $row)
	{
		if (substr($row->component,0,4) == 'com_') {
			$row->component = substr($row->component,4);
		}

		if ($row->component == 'support') {
			$fg = explode(' ',$row->subject);
			$fh = array_pop($fg);
			$row->subject = implode(' ',$fg);
		}

		$cls = (($cls == 'even') ? 'odd' : 'even');
?>
					<tr class="<?php echo $cls; ?>">
						<td><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages&msg='.$row->id); ?>"><?php echo stripslashes($row->subject); ?></a></td>
						<td><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$row->uid); ?>"><?php echo stripslashes($row->name); ?></a></td>
						<td><?php echo JHTML::_('date', $row->created, '%d %b, %Y'); ?></td>
					</tr>
<?php
	}
} else { ?>
					<tr class="odd">
						<td colspan="3"><?php echo JText::_('PLG_MEMBERS_MESSAGES_NONE'); ?></td>
					</tr>
<?php } ?>
				</tbody>
			</table>
		</form>
	</div><!-- / .subject -->
</div><!-- / .withleft -->
