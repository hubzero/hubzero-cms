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

$this->css('offering');
?>
<header id="content-header"<?php if ($this->course->get('logo')) { echo ' class="with-identity"'; } ?>>
	<h2>
		<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
	</h2>

	<?php if ($logo = $this->course->logo()) { ?>
	<p class="course-identity">
		<img src="<?php echo $logo; ?>" alt="<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>" />
	</p>
	<?php } ?>

	<p id="page_identity">
		<a class="prev" href="<?php echo JRoute::_($this->course->link()); ?>">
			<?php echo JText::_('COM_COURSES_COURSE_OVERVIEW'); ?>
		</a>
		<strong>
			<?php echo JText::_('COM_COURSES_OFFERING'); ?>:
		</strong>
		<span>
			<?php echo $this->escape(stripslashes($this->course->offering()->get('title'))); ?>
		</span>
		<strong>
			<?php echo JText::_('COM_COURSES_SECTION'); ?>:
		</strong>
		<span>
			<?php echo $this->escape(stripslashes($this->course->offering()->section()->get('title'))); ?>
		</span>
	</p>
</header><!-- #content-header -->

<section class="main section enroll-closed">
	<div class="section-inner">
		<div id="offering-introduction">
			<div class="instructions">
				<p class="warning"><?php echo JText::_('COM_COURSES_ENROLLMENT_CLOSED'); ?></p>
			</div><!-- / .instructions -->
			<div class="questions">
				<p><strong><?php echo JText::_('COM_COURSES_I_SHOULD_HAVE_ACCESS'); ?></strong></p>
				<p><?php echo JText::sprintf('COM_COURSES_I_SHOULD_HAVE_ACCESS_EXPLANATION', JRoute::_('index.php?option=com_support')); ?></p>
				<p><strong><?php echo JText::_('COM_COURSES_WHERE_CAN_I_FIND_THER_COURSES'); ?></strong></p>
				<p><?php echo JText::sprintf('COM_COURSES_WHERE_CAN_I_FIND_THER_COURSES_EXPLANATIONS', JRoute::_('index.php?option=' . $this->option . '&controller=courses&task=browse')); ?></p>
			</div><!-- / .questions -->
		</div><!-- / #offering-introduction -->
	</div>
</section><!-- /.main section -->