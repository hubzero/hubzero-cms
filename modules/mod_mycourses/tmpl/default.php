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

$juser = JFactory::getUser();

$total = count($this->courses);

?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : '';?>>

<ul class="module-nav">
	<li>
		<a class="icon-browse" href="<?php echo JRoute::_('index.php?option=com_courses&task=browse'); ?>">
			<?php echo JText::_('MOD_MYCOURSES_ALL_COURSES'); ?>
		</a>
	</li>
</ul>

<?php if ($this->courses && $total > 0) { ?>
	<ul class="compactlist">
<?php
	$i = 0;
	foreach ($this->courses as $course)
	{
		if ($i < $this->limit) {
			$sfx = '';

			if (isset($course->offering_alias))
			{
				$sfx .= '&offering=' . $course->offering_alias;
			}
			if (isset($course->section_alias) && !$course->is_default)
			{
				$sfx .= ':' . $course->section_alias;
			}
			//$status = $this->getStatus($group);
?>
		<li class="course">
			<a href="<?php echo JRoute::_('index.php?option=com_courses&gid=' . $course->alias . $sfx); ?>"><?php echo $this->escape(stripslashes($course->title)); ?></a>
			<?php if ($course->section_title) { ?>
			<small><strong><?php echo JText::_('MOD_MYCOURSES_SECTION'); ?></strong> <?php echo $this->escape($course->section_title); ?></small>
			<?php } ?>
			<?php
			switch ($course->state)
			{
				case 3: ?><small><?php echo JText::_('MOD_MYCOURSES_COURSE_STATE_DRAFT'); ?></small><?php break;
				case 2: ?><small><?php echo JText::_('MOD_MYCOURSES_COURSE_STATE_DELETED'); ?></small><?php break;
				case 1: ?><small><?php echo JText::_('MOD_MYCOURSES_COURSE_STATE_PUBLISHED'); ?></small><?php break;
				case 0: ?><small><?php echo JText::_('MOD_MYCOURSES_COURSE_STATE_UNPUBLISHED'); ?></small><?php break;
			}
			?>
			<span><span class="<?php echo $this->escape($course->role); ?> status"><?php echo $this->escape($course->role); ?></span></span>
		</li>
<?php
			$i++;
		}
	}
?>
	</ul>
<?php } else { ?>
	<p><em><?php echo JText::_('MOD_MYCOURSES_NO_RESULTS'); ?></em></p>
<?php } ?>

<?php if ($total > $this->limit) { ?>
	<p class="note"><?php echo JText::sprintf('MOD_MYCOURSES_YOU_HAVE_MORE', $this->limit, $total, JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=courses')); ?></p>
<?php } ?>
</div>