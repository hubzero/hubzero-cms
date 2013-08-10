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
		<li><a class="active" href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->course->get('cn').'&active=messages'); ?>"><span><?php echo JText::_('PLG_COURSES_MESSAGES_SENT'); ?></span></a></li>
		<?php if($this->authorized == 'admin' || $this->authorized == 'manager') { ?>
			<li><a href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->course->get('cn').'&active=messages&task=new'); ?>"><span><?php echo JText::_('PLG_COURSES_MESSAGES_SEND'); ?></span></a></li>
		<?php } ?>
	</ul>
	<br class="clear" />
	<div class="container">
		<form action="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->course->get('cn').'&active=messages'); ?>" method="post">
		<table class="courses entries" summary="Courses this person is a member of">
			<caption><?php echo JText::_('PLG_COURSES_MESSAGE'); ?> <span><small>( <a href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->course->get('cn').'&active=messages'); ?>">&lsaquo; Back to Sent Messages</a> )</small></span></caption>
			<tbody>
				<tr>
					<th><?php echo JText::_('PLG_COURSES_MESSAGES_RECEIVED'); ?>:</th>
					<td><?php echo JHTML::_('date', $this->xmessage->created, $dateFormat, $tz); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('PLG_COURSES_MESSAGES_FROM'); ?>:</th>
					<td><?php echo $from; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('PLG_COURSES_MESSAGES_SUBJECT'); ?>:</th>
					<td><?php echo stripslashes($this->xmessage->subject); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('PLG_COURSES_MESSAGES_MESSAGE'); ?>:</th>
					<td><?php echo $this->xmessage->message; ?></td>
				</tr>
			</tbody>
		</table>
		</form>
	</div>
</div><!-- // .subject -->

