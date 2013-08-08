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
defined('_JEXEC') or die('Restricted access');

$dateFormat  = '%d %b, %Y';
$timeFormat  = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat  = 'd M, Y';
	$timeFormat  = 'h:i a';
	$tz = true;
}
?>
<h3 class="section-header">
	<a name="messages"></a>
	<?php echo JText::_('MESSAGES'); ?>
</h3>

<?php if ($this->authorized == 'manager') { ?>
<ul id="page_options">
	<li>
		<a id="new-group-message" class="icon-email message btn" href="<?php echo JRoute::_('index.php?option='.$option.'&cn='.$this->group->get('cn').'&active=messages&action=new'); ?>">
			<span><?php echo JText::_('PLG_GROUPS_MESSAGES_SEND'); ?></span>
		</a>
	</li>
</ul>
<?php } ?>

<div class="section">
	<div class="container">
		<form action="<?php echo JRoute::_('index.php?option='.$option.'&cn='.$this->group->get('cn').'&active=messages'); ?>" method="post">
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
					<?php if (count($this->rows) > 0) { ?>
						<?php foreach ($this->rows as $row) { ?>
							<tr>
								<td><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=messages&action=viewmessage&msg='.$row->id); ?>"><?php echo $this->escape(stripslashes($row->subject)); ?></a></td>
								<td><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by); ?>"><?php echo $this->escape(stripslashes($row->name)); ?></a></td>
								<td><time datetime="<?php echo $row->created; ?>"><?php echo JHTML::_('date', $row->created, $dateFormat, $tz); ?></time></td>
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
	
	<?php 
		$this->pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
		$this->pageNav->setAdditionalUrlParam('active', 'messages');

		echo $this->pageNav->getListFooter();
	?>
		<div class="clearfix"></div>
	</div><!-- / .container -->
</div><!-- / .section -->

