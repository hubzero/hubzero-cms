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

$this->css()
     ->js();
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
			<a href="<?php echo JRoute::_($course->link()); ?>">
				<div class="course-details">
					<div class="course-identity">
						<?php if ($logo = $course->logo()) { ?>
							<img src="<?php echo $logo; ?>" alt="<?php echo JText::_('PLG_COURSES_RELATED_LOGO'); ?>" />
						<?php } else { ?>
							<span></span>
						<?php } ?>
						<?php if ($course->get('rating', 0) > 4) { ?>
							<div>
								<strong><?php echo JText::_('PLG_COURSES_RELATED_TOP_RATED'); ?></strong> <span class="rating">&#x272D;&#x272D;&#x272D;&#x272D;&#x272D;</span>
							</div>
						<?php } else if ($this->course->get('popularity', 0) > 7) { ?>
							<div>
								<strong><?php echo JText::_('PLG_COURSES_RELATED_POPULAR'); ?></strong> <span class="popularity">&#xf091;</span>
							</div>
						<?php } ?>
					</div>
					<h4 class="course-title">
						<?php echo $this->escape(stripslashes($course->get('title'))); ?>
					</h4>
					<?php if ($course->get('blurb')) { ?>
						<p class="course-description">
							<?php echo \Hubzero\Utility\String::truncate($this->escape(stripslashes($course->get('blurb'))), 130); ?>
						</p>
					<?php } ?>
				</div>
			</a>
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