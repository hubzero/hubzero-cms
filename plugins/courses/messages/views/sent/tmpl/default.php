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

$dateFormat = '%d %b, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$tz = false;
}

?>
<a name="messages"></a>
<h3><?php echo JText::_('MESSAGES'); ?></h3>

<div class="subject">
	<ul class="entries-menu">
		<li><a class="active" href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->course->get('cn').'&active=messages'); ?>"><span><?php echo JText::_('PLG_COURSES_MESSAGES_SENT'); ?></span></a></li>
		<?php if($this->authorized == 'manager') { ?>
			<li><a id="new-course-message" href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->course->get('cn').'&active=messages&task=new'); ?>"><span><?php echo JText::_('PLG_COURSES_MESSAGES_SEND'); ?></span></a></li>
		<?php } ?>
	</ul>
	<br class="clear" />
	<div class="container">
		<form action="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->course->get('cn').'&active=messages'); ?>" method="post">
		<table class="courses entries" summary="Courses this person is a member of">
			<caption><?php echo JText::_('PLG_COURSES_MESSAGES_SENT'); ?> <span>(<?php echo count($this->rows); ?>)</span></caption>
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
							<td><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('cn').'&active=messages&task=viewmessage&msg='.$row->id); ?>"><?php echo stripslashes($row->subject); ?></a></td>
							<td><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$row->created_by); ?>"><?php echo stripslashes($row->name); ?></a></td>
							<td><?php echo JHTML::_('date', $row->created, $dateFormat, $tz); ?></td>
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
		$pagenavhtml = str_replace('courses/?','courses/'.$this->course->get('cn').'/messages/sent/?',$pagenavhtml);
		$pagenavhtml = str_replace('action=sent','',$pagenavhtml);
		$pagenavhtml = str_replace('&amp;&amp;','&amp;',$pagenavhtml);
		$pagenavhtml = str_replace('?&amp;','?',$pagenavhtml);
		echo $pagenavhtml;
	?>
	
</div><!-- // .subject -->

