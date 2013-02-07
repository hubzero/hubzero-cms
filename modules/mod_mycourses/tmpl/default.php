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

?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : '';?>>
<?php if ($this->courses && count($this->courses) > 0) { ?>
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
			if (isset($course->section_alias) && $course->section_alias != '__default')
			{
				$sfx .= ':' . $course->section_alias;
			}
			//$status = $this->getStatus($group);
?>
		<li class="course">
			<a href="<?php echo JRoute::_('index.php?option=com_courses&gid=' . $course->alias . $sfx); ?>"><?php echo stripslashes($course->title); ?></a>
			<span><span class="<?php echo $course->role; ?> status"><?php echo $course->role; ?></span></span>
		</li>
<?php
			$i++;
		}
	}
?>
	</ul>
<?php } else { ?>
	<p><?php echo JText::_('MOD_MYCOURSES_NO_RESULTS'); ?></p>
<?php } ?>

	<ul class="module-nav">
		<li><a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=courses'); ?>"><?php echo JText::_('MOD_MYCOURSES_ALL_MY_COURSES'); ?></a></li>
		<li><a href="<?php echo JRoute::_('index.php?option=com_courses'); ?>"><?php echo JText::_('MOD_MYCOURSES_ALL_COURSES'); ?></a></li>
	</ul>
</div>