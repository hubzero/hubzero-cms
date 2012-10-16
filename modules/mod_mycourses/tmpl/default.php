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

$juser =& JFactory::getUser();
$courses = $this->courses;
?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : '';?>>
<?php if ($courses && count($courses) > 0) { ?>
	<ul class="compactlist mycourses">
<?php
	$i = 0;
	foreach ($courses as $course)
	{
		if ($course->published && $i < $this->limit) {
			$status = $this->getStatus($course);
?>
		<li class="course">
			<a href="<?php echo JRoute::_('index.php?option=com_courses&gid=' . $course->cn); ?>"><?php echo stripslashes($course->description); ?></a>
			<span><span class="<?php echo $status; ?> status"><?php echo JText::_('MOD_MYCOURSES_STATUS_' . strtoupper($status)); ?></span></span>
<?php if ($course->regconfirmed && !$course->registered) { ?>
			<span class="actions">
				<a class="action-accept" href="<?php echo JRoute::_('index.php?option=com_courses&gid=' . $course->cn . '&task=accept'); ?>"><?php echo JText::_('MOD_MYCOURSES_ACTION_ACCEPT'); ?></a> 
<?php /*				<a class="action-cancel" href="<?php echo JRoute::_('index.php?option=com_courses&gid='.$course->cn.'&task=cancel'); ?>"><?php echo JText::_('MOD_MYCOURSES_ACTION_CANCEL'); ?></a> */ ?>
			</span>
<?php } ?>
		</li>
<?php
			$i++;
		}
	}
?>
	</ul>
<?php } else { ?>
	<p><?php echo JText::_('MOD_MYCOURSES_NO_COURSES'); ?></p>
<?php } ?>

	<ul class="module-nav">
		<li><a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=courses'); ?>"><?php echo JText::_('MOD_MYCOURSES_ALL_MY_COURSES'); ?></a></li>
		<li><a href="<?php echo JRoute::_('index.php?option=com_courses'); ?>"><?php echo JText::_('MOD_MYCOURSES_ALL_COURSES'); ?></a></li>
		<li><a href="<?php echo JRoute::_('index.php?option=com_courses&task=new'); ?>"><?php echo JText::_('MOD_MYCOURSES_NEW_COURSE'); ?></a></li>
	</ul>
</div>

