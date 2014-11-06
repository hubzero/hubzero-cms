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

	<?php if ($logo = $this->course->logo('url')) { ?>
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

<section class="main section enroll-restricted">
	<div class="section-inner">
		<?php
			foreach ($this->notifications as $notification)
			{
				echo '<p class="' . $notification['type'] . '">' . $notification['message'] . '</p>';
			}
		?>

		<form action="<?php echo JRoute::_($this->course->offering()->link() . '&task=enroll'); ?>" method="post" id="hubForm">
			<div class="explaination">
				<h3><?php echo JText::_('COM_COURSES_CODE_NOT_WORKING'); ?></h3>
				<p><?php echo JText::_('COM_COURSES_CODE_NOT_WORKING_EXPLANATION'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo JText::_('COM_COURSES_REDEEM_COUPON_CODE'); ?></legend>

				<p class="warning"><?php echo JText::_('COM_COURSES_ENROLLMENT_RESTRICTED'); ?></p>

				<label for="field-code">
					<?php echo JText::_('COM_COURSES_FIELD_COUPON_CODE'); ?> <span class="required"><?php echo JText::_('JREQUIRED'); ?></span>
					<input type="text" name="code" id="field-code" size="35" value="" />
				</label>
			</fieldset>
			<div class="clear"></div>

			<input type="hidden" name="offering" value="<?php echo $this->escape($this->course->offering()->get('alias') . ':' . $this->course->offering()->section()->get('alias')); ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>" />
			<input type="hidden" name="task" value="enroll" />

			<p class="submit">
				<input class="btn btn-success" type="submit" value="<?php echo JText::_('COM_COURSES_REDEEM'); ?>" />
			</p>
		</form>
	</div>
</section><!-- /.main section -->