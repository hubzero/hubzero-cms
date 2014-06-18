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

$this->css();
?>
<div id="related-courses" class="after section">
	<h3>
		<?php echo (count($this->ids) > 1) ? JText::_('PLG_COURSES_RELATED_OTHER_BY_INSTRUCTORS') : JText::_('PLG_COURSES_RELATED_OTHER_BY_INSTRUCTOR'); ?>
	</h3>
	<?php
	$i = 0;
	$cls = '';
	foreach ($this->courses as $course)
	{
		$course = new CoursesModelCourse($course);
		$i++;
		if ($i == 3)
		{
			$cls = ' omega';
			$i = 0;
		}
		if ($i == 1)
		{
		?>
	<div class="grid">
		<?php
		}
		?>
		<div class="course-block col span-third<?php if ($cls) { echo $cls; } ?>">
			<h4>
				<a href="<?php echo JRoute::_($course->link()); ?>">
					<?php echo $this->escape(stripslashes($course->get('title'))); ?>
				</a>
			</h4>
			<div class="content">
				<div class="description">
					<?php echo \Hubzero\Utility\String::truncate(stripslashes($course->get('blurb')), 500); ?>
				</div>
				<p class="action">
					<a class="btn" href="<?php echo JRoute::_($course->link() . '&active=overview'); ?>">
						<?php echo JText::_('PLG_COURSES_RELATED_OVERVIEW'); ?>
					</a>
				</p>
			</div><!-- / .content -->
		</div><!-- / .col -->
		<?php
		if ($i == 0 || $i == count($this->courses))
		{
		?>
	</div><!-- / .grid -->
		<?php
		}
	}
	?>
</div><!-- / #related-courses -->